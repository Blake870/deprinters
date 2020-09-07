<?php

namespace Swissup\SeoTemplates\Model;

use Magento\Catalog\Model\Product\Visibility as ProductVisibility;

class Generator extends \Magento\Framework\DataObject
{
    /**
     * @var Template
     */
    protected $template;

    /**
     * @var ResourceModel\Template\CollectionFactory
     */
    protected $templateCollectionFactory;

    /**
     * @var SeodataFactory
     */
    protected $seodataFactory;

    /**
     * @var LogFactory
     */
    protected $logFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param Template                                   $template
     * @param ResourceModel\Template\CollectionFactory   $templateCollectionFactory
     * @param SeodataFactory                             $seodataFactory
     * @param LogFactory                                 $logFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array                                      $data
     */
    public function __construct(
        Template $template,
        ResourceModel\Template\CollectionFactory $templateCollectionFactory,
        SeodataFactory $seodataFactory,
        LogFactory $logFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Psr\Log\LoggerInterface $logger,
        array $data = []
    ){
        $this->template = $template;
        $this->templateCollectionFactory = $templateCollectionFactory;
        $this->seodataFactory = $seodataFactory;
        $this->logFactory = $logFactory;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        return parent::__construct($data);
    }

    /**
     * Get page size
     *
     * @return int
     */
    public function getPageSize()
    {
        if (!$this->hasData('page_size')) {
            $this->setData('page_size', 33);
        }

        return $this->getData('page_size');
    }

    /**
     * Get current page
     *
     * @return int
     */
    public function getCurPage()
    {
        if (!$this->hasData('cur_page')) {
            $this->setData('cur_page', 1);
        }

        return $this->getData('cur_page');
    }

    /**
     * Get entityt type to process
     *
     * @return int
     */
    public function getEntityType()
    {
        if (!$this->hasData('entity_type')) {
            $this->setData('entity_type', 0);
        }

        return $this->getData('entity_type');
    }

    /**
     * Generate metadat using templates
     *
     * @return $this
     */
    public function generate()
    {
        $type = $this->getEntityType();
        $collection = $this->getCollectionForEntityType($type)
            // ->addFieldToFilter('entity_id', ['in' => [1, 2, 4, 6, 8]])
            ->setPage($this->getCurPage(), $this->getPageSize());

        $templateCollection = $this->getTemplateCollection()
            ->addFieldToFilter('entity_type', ['eq' => $this->getEntityType()]);

        $this->setProcessedItems(0);
        // backup current store
        $backupCurrentStore = $this->storeManager->getStore();
        foreach ($collection as $entity) {
            foreach ($this->storeManager->getStores(true) as $store) {
                try {
                    $entity->setStoreId($store->getId())->load($entity->getId());
                } catch (\Exception $e) {
                    $this->logException($e, $type, $entity, $store);
                    continue;
                }

                // skip store if product is not visible in store
                if ($entity->getVisibility() == ProductVisibility::VISIBILITY_NOT_VISIBLE) {
                    continue;
                }

                // change current store (required for configurable products)
                if ($store->getId() == 0) {
                    // for All Store View use default Default Store View
                    $this->storeManager->setCurrentStore(
                        $this->storeManager->getDefaultStoreView()
                    );
                } else {
                    // else use specified store view
                    $this->storeManager->setCurrentStore($store);
                }

                foreach ($templateCollection as $template) {
                    // skip template if it does not assigned to store
                    if (!in_array($store->getId(), $template->getStoreId())) {
                        continue;
                    }

                    // skip template if if does not meet conditions
                    if (!$template->getConditions()->validate($entity)) {
                        continue;
                    };

                    // generate SEO data using template
                    $generatedValue = $template->generate($entity);
                    $seodata = $this->seodataFactory->create();
                    $seodata->loadByEntityId(
                        $entity->getId(),
                        $template->getEntityType(),
                        $store->getId()
                    );

                    // save generated only if record exists or generated is not empty
                    if ($seodata->getId() || $generatedValue) {
                        $dataName = $this->template->getDataNameCode(
                            $template->getDataName()
                        );
                        $meta = $seodata->getMetadata();
                        $meta[$dataName] = $generatedValue;
                        $seodata->setMetadata($meta)->save();
                    }

                    // add log record
                    $log = $this->logFactory->create();
                    $log->setData(
                        [
                            'template_id' => $template->getId(),
                            'entity_id' => $entity->getId(),
                            'store_id' => $store->getId(),
                            'generated_value' => $generatedValue
                        ]
                    );
                    $log->save();
                }
            }

            $this->setProcessedItems($this->getProcessedItems() + 1);
        }
        $this->storeManager->setCurrentStore($backupCurrentStore);

        if ($this->getCurPage() >= $collection->getLastPageNumber()) {
            $this->setNextPage(false);
        } else {
            $this->setNextPage(
                $this->getCurPage() < 1
                ? 2
                : ($this->getCurPage() + 1)
            );
        }

        return $this;
    }

    /**
     * Get collection for specific entity type
     *
     * @param  int $entityType
     * @return mixed
     */
    public function getCollectionForEntityType($entityType)
    {
        if ($this->hasData('collectionFactories')) {
            $typeCode = $this->template->getEntityTypeCode($entityType);
            $factory = $this->getData("collectionFactories/{$typeCode}");
            if ($factory) {
                return $factory->create();
            }
        }

        return null;
    }

    /**
     * Get entity type name
     *
     * @param  int $entityType
     * @return mixed
     */
    public function getEntityTypeName($entityType, $translateLabel = true)
    {
        return $this->template->getEntityTypeName($entityType, $translateLabel);
    }

    /**
     * Get SEO Templates collection (only active)
     *
     * @return ResourceModel\Template\Collection
     */
    public function getTemplateCollection()
    {
        $collection = $this->templateCollectionFactory->create()
            ->addFieldToFilter('status', ['eq' => 1])
            ->setOrder('priority', \Magento\Framework\Data\Collection::SORT_ORDER_ASC);
        return $collection;
    }

    /**
     * Available entity types for template
     *
     * @return array
     */
    public function getAvailableEntityTypes($translateLabel = true)
    {
        return $this->template->getAvailableEntityTypes($translateLabel);
    }

    /**
     * Clear templates logs where entity_type in $entityType
     *
     * @param  array $entityTypes
     * @return $this
     */
    public function claerTemplatesLogs($entityTypes = [])
    {
        $collection = $this->getTemplateCollection();
        $collection->addFieldToFilter('entity_type', ['in' => $entityTypes]);
        foreach ($collection as $template) {
            $template->clearLog();
        }

        return $this;
    }

    /**
     * Clear generated data for emtity types in $entityTypes
     *
     * @param  array  $entityTypes
     * @return $this
     */
    public function clearGeneratedData($entityTypes = [])
    {
        $seodata = $this->seodataFactory->create();
        $seodata->deleteGenerated($entityTypes);
        return $this;
    }

    /**
     * Log exception into system.log
     *
     * @param  \Exception                    $exception
     * @param  string                        $entityType
     * @param  \Magento\Framework\DataObject $entity
     * @param  \Magento\Store\Model\Store    $store
     */
    protected function logException(
        \Exception $exception,
        $entityType,
        \Magento\Framework\DataObject $entity,
        \Magento\Store\Model\Store $store
    ) {
        $msg = $this->hasData('exceptions')
            ? $this->getData('exceptions/load_failed')
            : '';
        $this->logger->critical(
            __(
                $msg,
                $this->getEntityTypeName($entityType),
                $entity->getId(),
                $store->getId()
            ),
            [
                'details' => $exception->getMessage()
            ]
        );
    }
}

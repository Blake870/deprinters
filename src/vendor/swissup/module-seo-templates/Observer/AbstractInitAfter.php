<?php

namespace Swissup\SeoTemplates\Observer;

use Magento\Framework\Event;
use Magento\Store\Model\ScopeInterface;
use Swissup\SeoTemplates\Model\SeodataBuilder;

abstract class AbstractInitAfter implements Event\ObserverInterface
{
    /**
     * @var SeodataBuilder
     */
    protected $seodataBuilder;

    /**
     * @var \Swissup\SeoTemplates\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $filterManager;

    /**
     * @param SeodataBuilder                          $seodataBuilder
     * @param \Swissup\SeoTemplates\Helper\Data       $helper
     * @param \Magento\Framework\Filter\FilterManager $filterManager
     */
    public function __construct(
        SeodataBuilder $seodataBuilder,
        \Swissup\SeoTemplates\Helper\Data $helper,
        \Magento\Framework\Filter\FilterManager $filterManager
    ) {
        $this->seodataBuilder = $seodataBuilder;
        $this->helper = $helper;
        $this->filterManager = $filterManager;
    }

    /**
     * Add meta data to category
     *
     * @param Event\Observer $observer
     * @return $this
     */
    abstract public function execute(Event\Observer $observer);

    /**
     * Update metadata for for $entity with generated metadata
     *
     * @param  \Magento\Catalog\Model\AbstractModel $entity
     * @param  int $entityType
     * @return void
     */
    protected function updateMetadata(
        \Magento\Catalog\Model\AbstractModel $entity,
        $entityType
    ) {
        $metadata = $this->collectMetadata($entity->getId(), $entityType);
        foreach ($metadata as $name => $value) {
            if ($value                             // there is generated metadata
                && (
                    empty($entity->getData($name)) // AND entity metadata is empty
                    || $this->helper->isForced()           // or force generated data enabled
                )
            ) {
                $entity->setData($name, $value);
            }
        }
    }

    /**
     * Collect generated metadata in arra where keys are metadata names.
     *
     * @param  int    $entityId
     * @param  string $entityType
     * @return array
     */
    private function collectMetadata($entityId, $entityType)
    {
        $allowed = ['meta_title', 'meta_description', 'meta_keywords'];
        $data = $this->seodataBuilder->get($entityId, $entityType)->toArray();

        return array_intersect_key($data, array_flip($allowed));
    }

    /**
     * Optimize metadata. Truncate it to fit max length settings.
     *
     * @param  \Magento\Catalog\Model\AbstractModel $entity
     * @return void
     */
    protected function optimizeMetadata(
        \Magento\Catalog\Model\AbstractModel $entity
    ) {
        $dataNames = ['meta_title', 'meta_description'];
        foreach ($dataNames as $name) {
            $value = $entity->getData($name);
            if (!$value) {
                continue;
            }

            $etc = $this->helper->getOptimizeEtc($name);
            $length = $this->helper->getOptimizeLength($name);
            $value = $this->filterManager->truncate(
                $value,
                ['length' => $length, 'breakWords' => false, 'etc' => $etc]
            );
            $entity = $entity->setData($name, $value);
        }
    }
}

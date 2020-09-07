<?php

namespace Swissup\SeoTemplates\Block\Adminhtml\Metadata;

use Swissup\SeoTemplates\Model\Template as SeoTemplate;
use Swissup\SeoTemplates\Model\ResourceModel\Seodata\CollectionFactory as SeodataCollectionFactory;

abstract class AbstractBlock extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var SeodataCollectionFactory
     */
    protected $seodataCollectionFactory;

    /**
     * @var SeoTemplate
     */
    protected $seoTemplate;

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * {@inheritdoc}
     */
    protected $_template = 'metadata.phtml';

    /**
     * @param \Magento\Framework\Registry $registry
     * @param SeodataCollectionFactory $seodataCollectionFactory
     * @param SeoTemplate $seoTemplate
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        SeodataCollectionFactory $seodataCollectionFactory,
        SeoTemplate $seoTemplate,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->seodataCollectionFactory = $seodataCollectionFactory;
        $this->seoTemplate = $seoTemplate;
        $this->systemStore = $systemStore;
        return parent::__construct($context, $data);
    }

    /**
     * Get current entity type
     *
     * @return int
     */
    abstract public function getCurrentEntityType();

    /**
     * Get courrent entity
     *
     * @return \Magento\Catalog\Model\AbstractModel
     */
    abstract public function getCurrentEntity();

    /**
     * Retrieve generated metadat for current entity
     *
     * @return \Swissup\SeoTemplates\Model\Seodata
     */
    public function getGeneratedData()
    {
        if (!$this->hasData('generated_seodata')) {
            $collection = $this->seodataCollectionFactory->create()
                ->addFilter('entity_type', $this->getCurrentEntityType())
                ->addFilter('entity_id', $this->getCurrentEntity()->getId())
                ->addStoreFilter($this->getCurrentEntity()->getStoreId())
                ->setOrder('store_id', 'DESC');
            $item = $collection->getFirstItem();
            $item->getResource()->unserializeFields($item);
            $this->setData('generated_seodata',$item);
        }

        return $this->getData('generated_seodata');
    }

    /**
     * Get available metadata names
     *
     * @return array
     */
    public function getAvailableDataNames()
    {
        return $this->seoTemplate->getAvailableDataNames();
    }

    /**
     * Get string code for seodata nane
     *
     * @param  int $seodataName
     * @return string
     */
    public function getDataNameCode($seodataName)
    {
        return $this->seoTemplate->getDataNameCode($seodataName);
    }

    /**
     * Get store vire label
     *
     * @param  string|int $storeId
     * @return string
     */
    public function getStoreViewLabel($storeId = '0')
    {
        if ($storeId == 0) {
            return __('All Store Views');
        }

        return $this->systemStore->getStoreName($storeId);
    }

    /**
     * Gte comma separated string of store view labels
     *
     * @return string
     */
    public function getStoreViewsList()
    {
        $label = [];
        $collection = $this->seodataCollectionFactory->create()
            ->addFilter('entity_type', $this->getCurrentEntityType())
            ->addFilter('entity_id', $this->getCurrentEntity()->getId())
            ->setOrder('store_id', 'ASC');
        foreach ($collection as $seodata) {
            $label[] = $this->getStoreViewLabel($seodata->getStoreId());
        }

        return implode(', ', $label);
    }
}

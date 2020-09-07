<?php

namespace Swissup\SeoUrls\Block\Adminhtml\Attribute;

class Labels extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var string
     */
    protected $_template = 'Swissup_SeoUrls::product/attribute/labels.phtml';

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Swissup\SeoUrls\Model\AttributeFactory $seoAttributeFactory
     * @param array $data
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Swissup\SeoUrls\Model\ResourceModel\Attribute\Action $attributeAction,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->attributeAction = $attributeAction;
    }

    /**
     * Retrieve stores collection with default store
     *
     * @return \Magento\Store\Model\ResourceModel\Store\Collection
     */
    public function getStores()
    {
        if (!$this->hasStores()) {
            $stores = $this->_storeManager->getStores(true);
            ksort($stores);
            $this->setData('stores', $stores);
        }
        return $this->_getData('stores');
    }

    /**
     * Retrieve in-URL labels of attribute for each store
     *
     * @return array
     */
    public function getLabelValues()
    {
        $values = [];
        $storeLabels = $this->attributeAction->getInUrlLabels(
            $this->getAttributeObject()
        );
        foreach ($this->getStores() as $store) {
            $values[$store->getId()] = isset($storeLabels[$store->getId()])
                ? $storeLabels[$store->getId()]['value']
                : '';
        }

        return $values;
    }

    /**
     * Retrieve attribute object from registry
     *
     * @return \Magento\Eav\Model\Entity\Attribute\AbstractAttribute
     */
    private function getAttributeObject()
    {
        return $this->registry->registry('entity_attribute');
    }
}

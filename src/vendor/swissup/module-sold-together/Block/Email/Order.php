<?php
/**
 * Copyright © 2015 Swissup. All rights reserved.
 */
namespace Swissup\SoldTogether\Block\Email;

class Order extends \Magento\Catalog\Block\Product\ProductList\Related
{
    /**
     * Name of table in DB
     *
     * @var string
     */
    protected $_tableName = 'swissup_soldtogether_order';

    /**
     * @return $this
     */
    protected function _prepareData()
    {
        if (!$order = $this->getOrder()) {
            return false;
        }
        $items = $order->getAllVisibleItems();
        $ids = [];
        foreach ($items as $item) {
            $ids[] = $item->getProductId();
        }
        /* @var $product \Magento\Catalog\Model\Product */
        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productCollection = $_objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection');
        $resource = $_objectManager->get('Magento\Framework\App\ResourceConnection');
        $this->_itemCollection = $productCollection->addAttributeToSelect(
            'required_options'
        )->addStoreFilter();

        if ($this->moduleManager->isEnabled('Magento_Checkout')) {
            $this->_addProductAttributesAndPrices($this->_itemCollection);
        }
        $this->_itemCollection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        $this->_itemCollection->getSelect()
            ->joinInner(
                ['so' => $resource->getTableName($this->_tableName)],
                'so.related_id=e.entity_id',
                ['soldtogether_weight' => 'so.weight']
            );

        $this->_itemCollection->getSelect()
            ->where('so.product_id in (?)', $ids)
            ->order('soldtogether_weight ' . \Magento\Framework\DB\Select::SQL_DESC);
        $this->_itemCollection->getSelect()->limit($this->getEmailLimit());

        $this->_itemCollection->load();

        foreach ($this->_itemCollection as $product) {
            $product->setDoNotUseCategoryId(true);
        }

        return $this;
    }

    public function getProductCollection()
    {
        return $this->_itemCollection;
    }

    /**
     * Get product limit in email
     *
     * @param  string $key
     * @return string
     */
    public function getEmailLimit()
    {
        return $this->_scopeConfig->getValue(
            "soldtogether/email/order_count",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}

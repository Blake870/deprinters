<?php

namespace Swissup\Highlight\Block\ProductList;

class Bestsellers extends Popular
{
    const PAGE_TYPE = 'bestsellers';

    protected $widgetPageVarName = 'hbp';

    protected $widgetPriceSuffix = 'bestsellers';

    protected $widgetCssClass = 'highlight-bestsellers';

    public function getProductCollectionType()
    {
        return \Swissup\Highlight\Model\ResourceModel\Product\CollectionFactory::TYPE_BESTSELLERS;
    }

    /**
     * Apply additional filers for popularity collection
     */
    protected function applyAdditionalFilters($collection)
    {
        // locale date is not used, because save does not use it too.
        // @see /Magento/Reports/Model/Product/Index/AbstractIndex::beforeSave
        $dateFrom = (new \DateTime())
            ->sub(new \DateInterval($this->getPeriod()))
            ->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
        $collection->getSelect()->where('order.created_at > ?', $dateFrom);
        return $this;
    }
}

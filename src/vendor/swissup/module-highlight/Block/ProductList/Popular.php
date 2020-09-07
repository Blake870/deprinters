<?php

namespace Swissup\Highlight\Block\ProductList;

class Popular extends All
{
    const PAGE_TYPE = 'popular';

    protected $widgetPageVarName = 'hpp';

    protected $widgetPriceSuffix = 'popular';

    protected $widgetCssClass = 'highlight-popular';

    public function getProductCollectionType()
    {
        return \Swissup\Highlight\Model\ResourceModel\Product\CollectionFactory::TYPE_POPULAR;
    }

    /**
     * @param  \Swissup\Highlight\Model\ResourceModel\Product\Popular\Collection
     * @return void
     */
    public function prepareProductCollection($collection)
    {
        parent::prepareProductCollection($collection);
        $collection->filterByPopularity(
            $this->getMinPopularity(),
            $this->getMaxPopularity()
        );
        return $this->applyAdditionalFilters($collection);
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
        $collection->getSelect()->where('added_at > ?', $dateFrom);
        return $this;
    }

    protected function initToolbar($toolbar)
    {
        parent::initToolbar($toolbar);
        if ($toolbar->getCurrentOrder() === 'popularity') {
            $toolbar->setSkipOrder(true);
            $this->getProductCollection()->getSelect()->order('popularity DESC');
        }
    }

    public function getDefaultSortField()
    {
        return 'popularity';
    }

    public function getDefaultSortFieldLabel()
    {
        return __('Popularity');
    }

    public function getDefaultSortDirection()
    {
        return 'DESC';
    }

    public function getPeriod()
    {
        if (!$this->hasData('period')) {
            return 'P1M'; // 1 month
        }
        return $this->getData('period');
    }

    public function getMinPopularity()
    {
        if (!$this->hasData('min_popularity')) {
            return 1;
        }
        return $this->getData('min_popularity');
    }

    /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        if (false === $this->getIsWidget()) {
            return parent::getCacheKeyInfo();
        }

        $keyInfo = parent::getCacheKeyInfo();
        $keyInfo['period'] = $this->getPeriod();
        return $keyInfo;
    }
}

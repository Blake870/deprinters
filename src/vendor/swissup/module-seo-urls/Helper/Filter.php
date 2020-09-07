<?php

namespace Swissup\SeoUrls\Helper;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Swissup\SeoUrls\Model\Filter\FilterFactories;

class Filter extends Data
{
    /**
     * @var
     */
    protected $attributeFiltersList;

    /**
     * @var array
     */
    protected $filters = [];

    /**
     * @var \Swissup\SeoUrls\Model\Filter\FilterFactories
     */
    protected $filterFactories;

    /**
     * @var \Magento\Framework\App\AreaList
     */
    protected $areaList;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    /**
     * @var \Swissup\SeoUrls\Model\Attribute
     */
    protected $seoAttribute;

    /**
     * Constructor
     *
     * @param \Magento\Catalog\Model\Layer\Category\FilterableAttributeList $filters
     * @param \Magento\Framework\Registry $registry
     * @param FilterFactories $filterFactories
     * @param \Swissup\SeoUrls\Model\Layer\PredefinedFilters $predefinedFilters
     * @param CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Category\FilterableAttributeList $filters,
        FilterFactories $filterFactories,
        \Magento\Framework\App\AreaList $areaList,
        \Magento\Framework\App\State $appState,
        \Swissup\SeoUrls\Model\Attribute $seoAttribute,
        Context $seoUrlsContext,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->attributeFiltersList = $filters->getList();
        $this->filterFactories = $filterFactories;
        $this->areaList = $areaList;
        $this->appState = $appState;
        $this->seoAttribute = $seoAttribute;
        parent::__construct(
            $seoUrlsContext,
            $context
        );
    }

    /**
     * Get Seo filter by name
     *
     * @param  string $name
     * @param  int $categoryId
     * @return Swissup\SeoUrls\Model\Filter\AbstractFilter
     */
    public function getByName($name)
    {
        if (!array_key_exists($name, $this->filters)) {
            // try to find predefined filter: category, stock, rating
            foreach ($this->predefinedFiltersList->getData() as $filterName => $filter) {
                if ($name == $filter->getRequestVar()) {
                    $this->filters[$name] = $this->createSeoFilter(
                        $filterName,
                        $filter->getAttributeCode()
                    );
                    break;
                }
            }

            // its not predefined filter so create regular attribute filter
            if (!isset($this->filters[$name])) {
                $this->filters[$name] = $this->createSeoFilter(
                    'attribute_filter',
                    $name
                );
            }
        }

        return $this->filters[$name];
    }

    /**
     * Find layer filter in string
     *
     * @param  string $string
     * @return string
     */
    public function findFilterInString($string)
    {
        // try to find predefined filter
        $this->loadTranslates();
        foreach ($this->predefinedFiltersList->getData() as $filter) {
            $seoLabel = '';
            if ($filter->hasAttributeCode()) {
                $seoLabel = $this->seoAttribute->getInUrlLabel($filter);
            }

            $seoLabel = $seoLabel
                ? $seoLabel
                : $this->getSeoFriendlyString($filter->getStoreLabel());
            if ($seoLabel && strpos($string, $seoLabel) === 0) {
                return $filter->getRequestVar();
            }
        }

        // try to find attribute filter
        $name = '';
        $matchedLabel = '';
        $uniqueStoreLabels = [];
        foreach ($this->attributeFiltersList as $filter) {
            $storeLabel = $this->seoAttribute->getStoreLabel($filter);
            if (!in_array($storeLabel, $uniqueStoreLabels)) {
                // store label is unique; use store label as seo label
                $seoLabel = $storeLabel;
                array_push($uniqueStoreLabels, $storeLabel);
            } else {
                // store label is not unique; use attribute code as seo label
                $seoLabel = $filter->getAttributeCode();
            }

            if (strpos($string, $seoLabel) === 0 && $seoLabel > $matchedLabel) {
                $matchedLabel = $seoLabel;
                $name = $filter->getName();
            }
        }

        return $name;
    }

    /**
     * Create SEO filter
     *
     * @param  string $filterName
     * @param  string $attributeName
     * @return Swissup\SeoUrls\Model\Filter\AbstractFilter | null
     */
    public function createSeoFilter($filterName, $attributeName = null)
    {
        if (isset($attributeName)) {
            $storeLabels = [];
            foreach ($this->attributeFiltersList as $f) {
                $isFilterLabelUnique = !in_array($f->getStoreLabel(), $storeLabels);
                if ($f->getName() == $attributeName) {
                    $seoFilter = $this->filterFactories
                        ->createFilter($filterName)
                        ->setLayerFilter($f);
                    // prevent seo labels overlapping
                    if (!$isFilterLabelUnique) {
                        $seoFilter->setLabel($f->getAttributeCode());
                    }

                    return $seoFilter;
                }

                if ($isFilterLabelUnique) {
                    array_push($storeLabels, $f->getStoreLabel());
                }
            }

            return null; // there are no attribute with $attributeName
        }

        return $this->filterFactories->createFilter($filterName);
    }

    /**
     * Force to load translates
     *
     * @return $this
     */
    protected function loadTranslates()
    {
        // no need to check if already loaded; area class implemented this check
        $area = $this->areaList->getArea($this->appState->getAreaCode());
        $area->load(\Magento\Framework\App\Area::PART_TRANSLATE);
        return $this;
    }
}

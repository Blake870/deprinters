<?php

namespace Swissup\SeoTemplates\Model\Filter;

use Swissup\SeoCore\Model\Filter\AbstractFilter;
use Magento\Catalog\Model\Product\Visibility as ProductVisibility;

class Category extends AbstractFilter
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Model\Layer\Resolver
     */
    protected $layerResolver;

    /**
     * @var \Magento\Catalog\Model\Layer\FilterList
     */
    protected $filterList;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Layer\Filter\Attribute
     */
    protected $filterResourceModel;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface                  $storeManager
     * @param \Magento\Catalog\Model\Layer\Resolver                       $layerResolver
     * @param \Magento\Catalog\Model\Layer\FilterList                     $filterList
     * @param \Magento\Catalog\Model\ResourceModel\Layer\Filter\Attribute $filterResourceModel
     * @param \Magento\Framework\Filter\FilterManager                     $filterManager
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Catalog\Model\Layer\FilterList $filterList,
        \Magento\Catalog\Model\ResourceModel\Layer\Filter\Attribute $filterResourceModel,
        \Magento\Framework\Filter\FilterManager $filterManager
    ) {
        $this->storeManager = $storeManager;
        $this->layerResolver = $layerResolver;
        $this->filterList = $filterList;
        $this->filterResourceModel = $filterResourceModel;
        parent::__construct($filterManager);
    }

    /**
     * Get option label by its value
     *
     * @param  array $options
     * @param  string $value
     * @return string
     */
    protected function _getOptionLabelByValue($options, $value)
    {
        $label = '';
        if (is_array($options)) {
            foreach ($options as $option) {
                if (isset($option['value']) && $option['value'] == $value) {
                    $label = isset($option['label']) ? $option['label'] : '';
                    break;
                }
            }
        }

        return $label;
    }

    /**
     * Fallback for products directive when attribute is not filterable
     *
     * @param  string $attributeCode
     * @return array
     */
    protected function _fallbackForNotFilterableAttribute($attributeCode)
    {
        $productCollection = $this->getScope()->getProductCollection();
        $productCollection->addAttributeToFilter('status', 1)
            ->addAttributeToFilter(
                'visibility',
                [
                    ProductVisibility::VISIBILITY_IN_CATALOG,
                    ProductVisibility::VISIBILITY_BOTH
                ]
            );
        $result = [];
        foreach ($productCollection as $product) {
            $product->load($product->getId());
            $value = $product->getResource()->getAttribute($attributeCode)
                ->getFrontend()->getValue($product);
            $value = (string)$value;
            if (isset($result[$value])) {
                $result[$value]++;
            } else {
                $result[$value] = 1;
            }
        }

        arsort($result);
        return array_keys($result);
    }

    /**
     * Products directive
     *
     * @param  array $construction
     * @return string
     */
    public function productsDirective($construction)
    {
        $params = $this->_getIncludeParameters($construction[2]);
        $attributeCode = isset($params['attribute']) ? $params['attribute'] : '';
        // attribute code can containe multiple attributes separated by comma
        $attributeCodes = array_map('trim', explode(",",$attributeCode));
        $layer = $this->layerResolver->get();
        $layer->setCurrentCategory($this->getScope());
        $result = [];
        $filterableAttributes = $layer->getFilterableAttributes();
        foreach ($attributeCodes as $code) {
            foreach ($this->filterList->getFilters($layer) as $filter) {
                if ($filter->hasAttributeModel()
                    && $filter->getAttributeModel()->getAttributeCode() == $code
                ) {
                    $optionsCount = $this->filterResourceModel->getCount($filter);
                    arsort($optionsCount);
                    $options = $filter->getAttributeModel()
                        ->getFrontend()
                        ->getSelectOptions();
                    foreach ($optionsCount as $id => $count) {
                        $label = $this->_getOptionLabelByValue($options, $id);
                        if ($label) {
                            $result[] = $label;
                        }
                    }
                }
            }

            if (empty($result)) {
                $result = $this->_fallbackForNotFilterableAttribute($code);
            }

            if (!empty($result)) {
                break;
            }
        }

        return $this->_postprocessResult($result, $params);
    }

    /**
     * Sub-categories directive - return child categories
     *
     * @param  array $construction
     * @return string
     */
    public function subcatsDirective($construction)
    {
        $params = $this->_getIncludeParameters($construction[2]);
        $result = array();
        foreach ($this->getScope()->getChildrenCategories() as $subcat) {
            $result[] = $subcat->getName();
        }

        return $this->_postprocessResult($result, $params);
    }

    /**
     * MinPrice directive
     *
     * @param  array $construction
     * @return string
     */
    public function minpriceDirective($construction)
    {
        $layer = $this->layerResolver->get();
        $layer->setCurrentCategory($this->getScope());
        $productCollection = $layer->getProductCollection();
        $params = $this->_getIncludeParameters($construction[2]);
        return $this->_postprocessResult($productCollection->getMinPrice(), $params);
    }

    /**
     * Parent categories directive
     *
     * @param  array $construction
     * @return string
     */
    public function parentsDirective($construction)
    {
        $parents = [];
        $params = $this->_getIncludeParameters($construction[2]);
        $depth = isset($params['depth']) ? intval($params['depth']) : 99;
        $direction = isset($params['direction']) ? $params['direction'] : 'from_assigned';

        $category = $this->getScope();

        $parent = $category->getParentId() ? $category->getParentCategory() : null;
        for ($i = 0; $i < 99 ; $i++) {
            if (!$parent
                || !$parent->getParentId()
                || $this->isRoot($parent)
            ) {
                break;
            }

            $parents[] = $parent->getName();
            $parent = $parent->getParentCategory();
        }

        if ($direction != 'from_assigned') {
            $parents = array_reverse($parents);
        }

        return $this->_postprocessResult(
            array_slice($parents, 0, $depth),
            $params
        );
    }

    /**
     * Check if category is root
     *
     * @param  \Magento\Catalog\Api\Data\CategoryInterface $category
     * @return boolean
     */
    private function isRoot($category)
    {
        $store = $this->storeManager->getStore($category->getStoreId());
        return $category->getId() == $store->getRootCategoryId();
    }
}

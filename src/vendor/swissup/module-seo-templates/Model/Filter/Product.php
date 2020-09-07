<?php

namespace Swissup\SeoTemplates\Model\Filter;

use Swissup\SeoCore\Model\Filter\AbstractFilter;

class Product extends AbstractFilter
{
    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Helper\Data
     */
    protected $catalogHelper;

    /**
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Store\Model\StoreManagerInterface       $storeManager
     * @param \Magento\Catalog\Helper\Data                     $catalogHelper
     * @param \Magento\Framework\Filter\FilterManager          $filterManager
     */
    public function __construct(
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Helper\Data $catalogHelper,
        \Magento\Framework\Filter\FilterManager $filterManager
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->storeManager = $storeManager;
        $this->catalogHelper = $catalogHelper;
        parent::__construct($filterManager);
    }

    /**
     * Price directive
     *
     * @param  array $construction
     * @return string
     */
    public function priceDirective($construction)
    {
        $params = $this->_getIncludeParameters($construction[2]);
        $finalPrice = $this->getScope()->getFinalPrice();
        if (isset($params['tax'])) {
            $includingTax = $params['tax'] == 'include'
                ? true
                : false;
            $finalPrice = $this->catalogHelper->getTaxPrice(
                $this->getScope(),
                $finalPrice,
                $includingTax
            );
        }

        return $this->_postprocessResult($finalPrice, $params);
    }

    /**
     * Categories directive
     *
     * @param  array $construction
     * @return string
     */
    public function categoriesDirective($construction)
    {
        $categories = [];
        $params = $this->_getIncludeParameters($construction[2]);
        $depth = isset($params['depth']) ? intval($params['depth']) : 99;
        $direction = isset($params['direction']) ? $params['direction'] : 'from_assigned';

        $product = $this->getScope();
        $parents = [];
        // Get caterory trees of all assigned categories.
        foreach ($product->getCategoryIds() as $id) {
            $category = $this->categoryRepository->get($id, $product->getStoreId());
            $parents[$id] = [];
            for ($i = 0; $i < 99 ; $i++) {
                if (!$category
                    || !$category->getParentId()
                    || $this->isRoot($category)
                ) {
                    break;
                }

                $parents[$id][] = $category->getName();
                $category = $category->getParentCategory();
            }

            $parents[$id] = array_reverse($parents[$id]);

        }

        $parents = $this->filterParentCategories($parents);
        $categories = [];
        foreach ($parents as $p) {
            if ($direction === 'from_assigned') {
                $p = array_reverse($p);
            }

            $categories = array_merge(
                $categories,
                array_slice($p, 0, $depth)
            );
        }

        return $this->_postprocessResult(array_unique($categories), $params);
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

    /**
     * Remove items with children that occurs in others
     *
     * @param  array  $categories
     * @return array
     */
    private function filterParentCategories(array $categories)
    {
        foreach ($categories as $id => $children) {
            $str = implode(',', $children) . ',';

            foreach ($categories as $children2) {
                $str2 = implode(',', $children2);
                if (strpos($str2, $str) === 0) {
                    $categories[$id] = [];
                    break;
                }
            }
        }

        return array_filter($categories);
    }
}

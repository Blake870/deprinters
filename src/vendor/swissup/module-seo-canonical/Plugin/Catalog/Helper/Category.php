<?php

namespace Swissup\SeoCanonical\Plugin\Catalog\Helper;

use Magento\Store\Model\Store;

class Category
{
    /**
     * @var \Swissup\SeoCanonical\Helper\Data
     */
    private $helper;

    /**
     * @param \Swissup\SeoCanonical\Helper\Data $helper
     */
    public function __construct(
        \Swissup\SeoCanonical\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Around plugin to disable default Magento Canonical URL
     *
     * @param  \Magento\Catalog\Helper\Category $subject
     * @param  callable                         $proceed
     * @param  null|string|bool|int|Store       $store
     * @return bool
     */
    public function aroundCanUseCanonicalTag(
        \Magento\Catalog\Helper\Category $subject,
        callable $proceed,
        $store = null
    ) {
        if ($this->helper->isCanonicalCategoryEnabled($store)) {
            // disable default Magento Canonical
            return false;
        }

        return $proceed($store);
    }
}

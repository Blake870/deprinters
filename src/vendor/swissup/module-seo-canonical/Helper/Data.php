<?php

namespace Swissup\SeoCanonical\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    const XML_PATH_CANONICAL_CATEGORY_ENABLED = 'swissup_seocanonical/category/enabled';

    const XML_PATH_CANONICAL_PRODUCT_ENABLED = 'swissup_seocanonical/product/enabled';

    const XML_PATH_CANONICAL_PRODUCT_USE_PARENT = 'swissup_seocanonical/product/use_parent';

    /**
     * @var \Swissup\SeoCanonical\Model\RegistryLocator
     */
    protected $locator;

    /**
     * Constructor
     *
     * @param \Swissup\SeoCanonical\Model\RegistryLocator $registryLocator
     * @param \Swissup\SeoCanonical\Model\ParentProduct   $parentProduct
     * @param \Magento\Cms\Model\Page                     $currentCmsPage
     * @param Context                                     $context
     */
    public function __construct(
        \Swissup\SeoCanonical\Model\RegistryLocator $registryLocator,
        \Swissup\SeoCanonical\Model\ParentProduct $parentProduct,
        \Magento\Cms\Model\Page $currentCmsPage,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Context $context
    ) {
        $this->locator = $registryLocator;
        $this->parentProduct = $parentProduct;
        $this->currentCmsPage = $currentCmsPage;
        $this->registry = $registry;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Is canonical URL for category enabled
     *
     * @param  null|string|bool|int $store
     * @return boolean
     */
    public function isCanonicalCategoryEnabled($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CANONICAL_CATEGORY_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Is canonical URL for product enabled
     *
     * @param  null|string|bool|int $store
     * @return boolean
     */
    public function isCanonicalProductEnabled($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CANONICAL_PRODUCT_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Can use parent product for product canonical URL
     *
     * @param  null|string|bool|int $store
     * @return boolean
     */
    public function canProductUseParent($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_CANONICAL_PRODUCT_USE_PARENT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get current category canonical URL
     *
     * @return string
     */
    public function getCatalogCategoryCanonicalUrl($ignoreRoot = true)
    {
        $category = $this->locator->getCategory();
        if ($ignoreRoot && $category && $this->isRoot($category)) {
            return '';
        }

        return $category ? $category->getUrl() : '';
    }

    /**
     * Get current product canonical URL
     *
     * @return string
     */
    public function getCatalogProductCanonicalUrl()
    {
        $product = $this->locator->getProduct();
        if ($product && $this->canProductUseParent()) {
            $parent = $this->parentProduct->get($product);
            $product = $parent ?: $product;
        }

        return $product
            ? $product->getUrlModel()->getUrl(
                $product,
                ['_ignore_category' => true]
            )
            : '';
    }

    /**
     * Get current CMS page canonical URL
     *
     * @return string
     */
    public function getCmsPageCanonicalUrl()
    {
        $canonicalUrl = '';
        if ($this->currentCmsPage->getId()) {
            $identifier = $this->currentCmsPage->getIdentifier();
            if ($identifier == $this->getHomepageIdentifier()) {
                $identifier = '';
            }

            $canonicalUrl = $this->_urlBuilder->getUrl(
                null,
                ['_direct' => $identifier]
            );
        }

        return $canonicalUrl;
    }

    /**
     * Get CMS page identifier for homapage
     *
     * @return string
     */
    public function getHomepageIdentifier()
    {
        return $this->scopeConfig->getValue(
            \Magento\Cms\Helper\Page::XML_PATH_HOME_PAGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
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

<?php

namespace Swissup\SeoUrls\Helper;

use Magento\Catalog\Api\CategoryRepositoryInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Swissup\SeoUrls\Model\Layer\PredefinedFilters
     */
    protected $predefinedFiltersList;

    /**
     * @var \Swissup\SeoCore\Model\Slug
     */
    protected $slug;

    /**
     * @param Context                               $seoUrlsContext
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        Context $seoUrlsContext,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->categoryRepository = $seoUrlsContext->getCategoryRepository();
        $this->storeManager = $seoUrlsContext->getStoreManager();
        $this->predefinedFiltersList = $seoUrlsContext->getPredefinedFilters();
        $this->slug = $seoUrlsContext->getSlug();
        parent::__construct($context);
    }

    /**
     * Convert given string into seo string for url
     *
     * @param  string $string
     * @return string
     */
    public function getSeoFriendlyString($string)
    {
        $slugified = $this->slug->slugify($string);
        // workaround to replace first dash with minus sign "−" (HTML: &#8722;)
        if (strpos($slugified, '-') === 0) {
            $slugified = '−' . substr($slugified, 1);
        }

        return $slugified;
    }

    /**
     * Get predefined layer filter seo label
     *
     * @param  string $filterName
     * @return string
     */
    public function getPredefinedFilterLabel($filterName)
    {
        if ($this->predefinedFiltersList->hasData($filterName)) {
            $label = $this->predefinedFiltersList->getData($filterName)
                ->getStoreLabel();
            return $this->getSeoFriendlyString($label);
        }

        return '';
    }

    /**
     * Get predefined layer filter request var
     *
     * @param  string $filterName
     * @return string
     */
    public function getPredefinedFilterRequestVar($filterName)
    {
        if ($this->predefinedFiltersList->hasData($filterName)) {
            return $this->predefinedFiltersList->getData($filterName)
                ->getRequestVar();
        }

        return '';
    }

    /**
     * Check if SEO URLs enabled
     *
     * @return boolean
     */
    public function isSeoUrlsEnabled()
    {
        return (bool)$this->scopeConfig->getValue(
            'swissup_seourls/general/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get name for search controller in URL
     *
     * @return string
     */
    public function getSearchControllerName()
    {
        $name = $this->getCurrentStore()->getConfig(
            'swissup_seourls/search/controller_name'
        );
        if (strpos($name, '.') === false) {
            // no extension
            $name = rtrim($name, '/') . '/';
        }

        return $name;
    }

    /**
     * Check config option if search term show in url body
     *
     * @return boolean
     */
    public function isSearchTermInUrl()
    {
        return (bool)$this->getCurrentStore()->getConfig(
            'swissup_seourls/search/term_place'
        );
    }

    /**
     * Get catgeory by its ID
     *
     * @param  int|string $id
     * @return \Magento\Catalog\Api\Data\CategoryInterface
     */
    public function getCategoryById($id)
    {
        return $this->categoryRepository->get(
            $id,
            $this->storeManager->getStore()->getId()
        );
    }

    /**
     * Get root catgeory for current store
     *
     * @return \Magento\Catalog\Api\Data\CategoryInterface
     */
    public function getRootCategory()
    {
        return $this->getCategoryById(
            $this->storeManager->getStore()->getRootCategoryId()
        );
    }

    /**
     * Is separate filter enabled
     *
     * @return boolean
     */
    public function isSeparateFilters()
    {
        $store = $this->getCurrentStore();
        return $this->scopeConfig->getValue(
                'swissup_seourls/layered_navigation/separate_filters',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $store->getCode()
            )
            &&
            $this->scopeConfig->getValue(
                'swissup_seourls/layered_navigation/separator',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $store->getCode()
            );
    }

    /**
     * Get filter separator
     *
     * @return string
     */
    public function getFiltersSeparator()
    {
        $store = $this->getCurrentStore();
        $separator = $this->scopeConfig->getValue(
            'swissup_seourls/layered_navigation/separator',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store->getCode()
        );
        return trim($separator, '/');
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
     * Get URL to homepage
     *
     * @return string
     */
    public function getHomepageUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl();
    }

    /**
     * Is homepage Redirect enabled
     *
     * @return boolean
     */
    public function isHomepageRedirect()
    {
        return $this->isSeoUrlsEnabled()
            && $this->scopeConfig->getValue(
                'swissup_seourls/cms/redirect_to_home',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
    }

    /**
     * Get current store
     *
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    public function getCurrentStore()
    {
        return $this->storeManager->getStore();
    }

}

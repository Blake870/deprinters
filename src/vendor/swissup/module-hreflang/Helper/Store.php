<?php

namespace Swissup\Hreflang\Helper;

use Magento\Store\Model\ScopeInterface;

class Store
{
    const XML_PATH_LOCALE_IN_URL = 'swissup_hreflang/url/add_locale';
    const XML_PATH_REMOVE_REGION = 'swissup_hreflang/url/remove_region';
    const XML_PATH_HREFLANG_IN_PAGE = 'swissup_hreflang/general/enabled';
    const XML_PATH_HREFLANG_IN_XMLSITEMAP = 'swissup_hreflang/general/enabled_xml';
    const XML_PATH_EXCLUDE_STORE = 'swissup_hreflang/general/excluded';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var boolean
     */
    protected $localeInUrlProcessed = false;

    /**
     * Constructor
     *
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * Get locale for store
     *
     * @param  \Magento\Store\Model\Store $store
     * @return string
     */
    public function getLocale(\Magento\Store\Model\Store $store)
    {
        $localePath = \Magento\Directory\Helper\Data::XML_PATH_DEFAULT_LOCALE;
        return $store->getConfig($localePath);
    }

    /**
     * Get hreflang for store
     *
     * @param  \Magento\Store\Model\Store $store
     * @return string
     */
    public function getHreflang(\Magento\Store\Model\Store $store)
    {
        $locale = $this->getLocale($store);
        $parts = explode('_', $locale);
        if (count($parts) == 2) {
            $parts = array_map('strtolower', $parts);
            $isRemoveRegion = $this->scopeConfig->isSetFlag(
                self::XML_PATH_REMOVE_REGION,
                ScopeInterface::SCOPE_STORE,
                $store->getCode()
            );
            if ($isRemoveRegion) {
                unset($parts[1]);
            }

            return implode('-', $parts);
        }

        return '';
    }

    /**
     * Is add locale in url enabled
     *
     * @param  \Magento\Store\Model\Store $store
     * @return boolean
     */
    public function isLocaleInUrl(\Magento\Store\Model\Store $store)
    {
        return $this->scopeConfig->isSetFlag(
                self::XML_PATH_LOCALE_IN_URL,
                ScopeInterface::SCOPE_STORE,
                $store->getCode()
            );
    }

    /**
     * Get store manager
     *
     * @return return \Magento\Store\Model\StoreManagerInterface
     */
    public function getStoreManager()
    {
        return $this->storeManager;
    }

    /**
     * Is $store Admin
     *
     * @param  \Magento\Store\Model\Store $store
     * @return boolean
     */
    public function isAdmin(\Magento\Store\Model\Store $store)
    {
        return $store->getCode() == \Magento\Store\Model\Store::ADMIN_CODE;
    }

    /**
     * Is redirect allowed
     *
     * @return boolean
     */
    public function isRedirectAllowed()
    {
        return $this->isLocaleInUrl($this->storeManager->getStore())
            && !$this->localeInUrlProcessed;
    }

    /**
     * Set value for flag locale_in_url_processed
     *
     * @param boolean $flag
     */
    public function setLocaleInUrlProcessed($flag = true)
    {
        $this->localeInUrlProcessed = $flag;
        return $this;
    }

    /**
     * Is add hreflang to page head for $store
     *
     * @param  \Magento\Store\Model\Store $store
     * @return boolean
     */
    public function isEnabledInPage(\Magento\Store\Model\Store $store)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_HREFLANG_IN_PAGE,
            ScopeInterface::SCOPE_STORE,
            $store->getCode()
        );
    }

    /**
     * Is add hreflang to XML Sitemap for $store
     *
     * @param  \Magento\Store\Model\Store $store
     * @return boolean
     */
    public function isEnabledInXmlSitemap(\Magento\Store\Model\Store $store)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_HREFLANG_IN_XMLSITEMAP,
            ScopeInterface::SCOPE_STORE,
            $store->getCode()
        );
    }

    /**
     * Get x-default store
     *
     * @return \Magento\Store\Model\Store|null
     */
    public function getXDefaultStore(\Magento\Store\Model\Store $store)
    {
        $id = $this->scopeConfig->getValue(
            'swissup_hreflang/general/default_store',
            ScopeInterface::SCOPE_STORE,
            $store->getCode()
        );

        $stores = $store->getWebsite()->getStores();
        return isset($stores[$id]) ? $stores[$id] : null;
    }

    /**
     * Is $store excluded from hreflang data
     *
     * @param  \Magento\Store\Model\Store $store
     * @return boolean
     */
    public function isExcluded(\Magento\Store\Model\Store $store)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_EXCLUDE_STORE,
            ScopeInterface::SCOPE_STORE,
            $store->getCode()
        );
    }
}

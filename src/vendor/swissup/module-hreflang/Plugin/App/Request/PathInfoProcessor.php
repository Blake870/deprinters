<?php
/**
 * Plugin for class \Magento\Store\App\Request\PathInfoProcessor
 */
namespace Swissup\Hreflang\Plugin\App\Request;

use Magento\Framework\App\Request\PathInfoProcessorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\Store;

class PathInfoProcessor extends \Swissup\Hreflang\Plugin\AbstractPlugin
{
    /**
     * @var string
     */
    private $originalDefaultScope;

    /**
     * @var string
     */
    private $originalStoreScope;

    /**
     * @var Store
     */
    private $currentStore;

    /**
     * @param \Magento\Store\Model\ResourceModel\Store\CollectionFactory $collectionFactory
     * @param \Magento\Framework\App\Config\ReinitableConfigInterface    $config
     * @param \Swissup\Hreflang\Helper\Store                             $helper
     */
    public function __construct(
        \Magento\Store\Model\ResourceModel\Store\CollectionFactory $collectionFactory,
        \Magento\Framework\App\Config\ReinitableConfigInterface $config,
        \Swissup\Hreflang\Helper\Store $helper
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->config = $config;
        parent::__construct($helper);
    }

    /**
     * Before for method 'process'; replace locale with store code
     *
     * @param  PathInfoProcessorInterface $subject
     * @param  RequestInterface $request
     * @param  string $pathInfo
     * @return array
     */
    public function beforeProcess(
        PathInfoProcessorInterface $subject,
        RequestInterface $request,
        $pathInfo
    ) {
        $pathParts = explode('/', ltrim($pathInfo, '/'), 2);
        $hreflang = isset($pathParts[0]) ? $pathParts[0] : '';
        $this->currentStore = $this->getStore($hreflang, $request);
        if ($this->currentStore && $this->helper->isLocaleInUrl($this->currentStore)) {
            $pathInfo = '/'
                . $this->currentStore->getCode()
                . '/'
                . (isset($pathParts[1]) ? $pathParts[1] : '');
            $this->helper->setLocaleInUrlProcessed();

            // Magento 2.3.x fix
            $this->forceStoreInUrl();
        }

        return [$request, $pathInfo];
    }

    /**
     * After for method 'process'. Restore config value
     *
     * @param  PathInfoProcessorInterface $subject
     * @param  string                     $result
     * @return string
     */
    public function afterProcess(
        PathInfoProcessorInterface $subject,
        $result
    ) {
        // Magento 2.3.x fix
        if ($this->currentStore && $this->helper->isLocaleInUrl($this->currentStore)) {
            $this->restoreConfig();
        }

        return $result;
    }

    /**
     * Get base URL from request
     *
     * @param  RequestInterface $request
     * @return string
     */
    private function getBaseUrl(RequestInterface $request)
    {
        $uri = $request->getUri();
        return $uri->getScheme()
             . '://'
             . $uri->getHost();
    }

    /**
     * @param  string           $hreflang
     * @param  RequestInterface $request
     * @return Store | null
     */
    protected function getStore($hreflang, RequestInterface $request)
    {
        $storeCollection = $this->collectionFactory->create();
        $currentBaseUrl = $this->getBaseUrl($request);
        foreach ($storeCollection as $store) {
            $storeBaseUrl = $store->getBaseUrl(
                UrlInterface::URL_TYPE_LINK,
                $this->isCurrentlySecure($store, $request)
            );
            if ($hreflang === $this->helper->getHreflang($store)
                && (strpos($storeBaseUrl, $currentBaseUrl) === 0)
            ) {
                return $store;
            }
        }

        return null;
    }

    /**
     * Is request secure for store
     *
     * Copy of method \Magento\Store\Model\Store::isCurrentlySecure().
     * Except read config values. Read config for specific store to prevent call
     * of \Magento\Store\Model\StoreResolver::getCurrentStoreId().
     *
     * @param  Store            $store
     * @param  RequestInterface $request
     * @return boolean
     */
    protected function isCurrentlySecure(Store $store, RequestInterface $request)
    {
        if ($request->isSecure()) {
            return true;
        }

        $secureBaseUrl = $store->getConfig(
            Store::XML_PATH_SECURE_BASE_URL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $secureFrontend = $store->getConfig(
            Store::XML_PATH_SECURE_IN_FRONTEND,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if (!$secureBaseUrl || !$secureFrontend) {
            return false;
        }

        $uri = \Zend_Uri::factory($secureBaseUrl);
        $port = $uri->getPort();
        $serverPort = $request->getServer('SERVER_PORT');
        $isSecure = $uri->getScheme() == 'https' && isset($serverPort) && $port == $serverPort;
        return $isSecure;
    }

    /**
     * Force 'store in URL' in config
     */
    private function forceStoreInUrl()
    {
        // Save original values.
        $this->originalDefaultScope = $this->config->getValue(Store::XML_PATH_STORE_IN_URL);
        $this->originalStoreScope = $this->config->getValue(
            Store::XML_PATH_STORE_IN_URL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->currentStore->getCode()
        );

        // Set store_in_url as true
        $this->config->setValue(Store::XML_PATH_STORE_IN_URL, '1');
        $this->config->setValue(
            Store::XML_PATH_STORE_IN_URL,
            '1',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->currentStore->getCode()
        );
    }

    /**
     * Restore config values after forcing 'store in URL'
     */
    private function restoreConfig()
    {
        $this->config->setValue(
            Store::XML_PATH_STORE_IN_URL,
            $this->originalDefaultScope
        );
        $this->config->setValue(
            Store::XML_PATH_STORE_IN_URL,
            $this->originalStoreScope,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->currentStore->getCode()
        );
    }
}

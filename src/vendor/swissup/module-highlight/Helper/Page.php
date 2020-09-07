<?php

namespace Swissup\Highlight\Helper;

use Magento\Framework\App\Action\Action;

class Page extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var array
     */
    protected $configValues;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->escaper = $escaper;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * @param Action $action
     * @param string $pageType
     * @return \Magento\Framework\View\Result\Page|bool
     */
    public function preparePage(Action $action, $pageType)
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        $config = $this->getConfigValues($pageType);
        $resultPage->getConfig()->getTitle()->set(
            $this->escaper->escapeHtml($config['title'])
        );

        $products = $resultPage->getLayout()->getBlock('category.products.list');
        if ($products) {
            $products
                ->setCacheLifetime(null)
                ->setIsWidget(false);

            if (isset($config['min_popularity'])) {
                $products->setMinPopularity((int)$config['min_popularity']);
            }
            if (isset($config['period'])) {
                $products->setPeriod($config['period']);
            }
        }

        return $resultPage;
    }

    /**
     * Retrieve config values for specific page type
     *
     * @param  string $pageType
     * @return array
     */
    public function getConfigValues($pageType = null)
    {
        if (!$this->configValues) {
            $this->configValues = $this->scopeConfig->getValue(
                'highlight',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }

        if (!$pageType) {
            return $this->configValues;
        }
        return $this->configValues[$pageType];
    }

    /**
     * Retrieve action name for specific page type
     *
     * @param  string $pageType
     * @return string
     */
    public function getActionName($pageType)
    {
        $config = $this->getConfigValues($pageType);
        return $config['action_name'];
    }

    /**
     * Retrieve page url for specific page type
     *
     * @param  string $pageType
     * @return string
     */
    public function getPageUrl($pageType)
    {
        $urlKey = $this->getUrlKey($pageType);
        if (!$urlKey) {
            return false;
        }
        return $this->_urlBuilder->getUrl(null, ['_direct' => $urlKey]);
    }

    /**
     * Retrieve direct url for for custom url
     *
     * @param  string $url
     * @return string
     */
    public function getDirectUrl($url)
    {
        return $this->_urlBuilder->getUrl(null, ['_direct' => $url]);
    }

    /**
     * Retrieve page url key for specific page type
     *
     * @param  string $pageType
     * @return string
     */
    public function getUrlKey($pageType)
    {
        $urlKeys = $this->getUrlKeys();
        if (!isset($urlKeys[$pageType])) {
            return null;
        }
        return $urlKeys[$pageType];
    }


    /**
     * Retrieve page url keys for every page type
     *
     * @param  string $pageType
     * @return array
     */
    public function getUrlKeys()
    {
        $urlKeys = [];
        foreach ($this->getConfigValues() as $pageType => $pageConfig) {
            if (!isset($pageConfig['url'])) {
                continue;
            }

            $urlKeys[$pageType] = $pageConfig['url'];
        }
        return $urlKeys;
    }

    public function getPageTypeByUrlKey($urlKey)
    {
        return array_search($urlKey, $this->getUrlKeys());
    }
}

<?php

namespace Swissup\Hreflang\Block;

use Magento\Framework\Session\SidResolverInterface;
use Magento\Store\Model\Store;

class Head extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Swissup\Hreflang\Helper\Store
     */
    protected $helper;

    /**
     * @var \Swissup\Hreflang\Model\CurrentUrl
     */
    protected $currentUrl;

    /**
     * @var string[]
     */
    private $hreflangs = [];

    /**
     * Construct
     *
     * @param \Swissup\Hreflang\Helper\Store                   $helper
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array                                            $data
     */
    public function __construct(
        \Swissup\Hreflang\Helper\Store $helper,
        \Swissup\Hreflang\Model\CurrentUrl $currentUrl,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->currentUrl = $currentUrl;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getRequest()->isXmlHttpRequest()
            || !$this->isEnabled()
        ) {
            return $this;
        }

        foreach ($this->_storeManager->getWebsite()->getStores() as $store) {
            if (!$this->helper->isExcluded($store)
                && $url = $this->getCurrentUrl($store)
            ) {
                $lang = $this->helper->getHreflang($store);
                $this->addLinkHreflang($lang, $url);
            }
        }

        // add x-default when config enabled
        $xDefaultStore = $this->getXDefaultStore();
        if ($xDefaultStore && $url = $this->getCurrentUrl($xDefaultStore)) {
            $this->addLinkHreflang('x-default', $url);
        }

        return $this;
    }

    /**
     * Add link to language
     *
     * @param string $language
     * @param string $href
     */
    public function addLinkHreflang($language, $href)
    {
        $this->pageConfig->addRemotePageAsset(
            $href,
            'hreflang-' . $language,
            [
                'attributes' => [
                    'rel' => 'alternate',
                    'hreflang' => $language
                ]
            ]
        );

        $this->hreflangs[$language] = $href;

        return $this;
    }

    /**
     * Get x-default store
     *
     * @return Store|null
     */
    public function getXDefaultStore()
    {
        $store = $this->_storeManager->getStore();
        return $this->helper->getXDefaultStore($store);
    }

    /**
     * Get current url in specific store (null when url not found)
     *
     * @param  Store $store
     * @return string|null
     */
    public function getCurrentUrl(Store $store)
    {
        return $this->currentUrl->get($store);
    }

    /**
     * Check if add hreflang enabled
     *
     * @param  Store   $store
     * @return boolean
     */
    public function isEnabled()
    {
        $store = $this->_storeManager->getStore();
        return $this->helper->isEnabledInPage($store);
    }

    /**
     * Get herflangs
     *
     * @return string[]
     */
    public function getHreflangs()
    {
        return $this->hreflangs;
    }
}

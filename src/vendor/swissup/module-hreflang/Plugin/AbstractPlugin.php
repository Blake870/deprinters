<?php

namespace Swissup\Hreflang\Plugin;

abstract class AbstractPlugin
{
    /**
     * @var \Swissup\Hreflang\Helper\Store
     */
    protected $helper;

    /**
     * Construct
     *
     * @param \Swissup\Hreflang\Helper\Store $helper [description]
     */
    public function __construct(\Swissup\Hreflang\Helper\Store $helper){
        $this->helper = $helper;
    }

    /**
     * Build URL for $pathInfo in specific $store
     *
     * @param  \Magento\Store\Model\Store $store
     * @param  string                     $pathInfo
     * @return string
     */
    protected function buildUrl(\Magento\Store\Model\Store $store, $pathInfo)
    {
        $query = [];
        if (!$store->isUseStoreInUrl()) {
            $query['___store'] = $store->getCode();
        }

        return $store->getBaseUrl()
            . $pathInfo
            . ($query ? '?' . http_build_query($query, '', '&amp;') : '');
    }
}

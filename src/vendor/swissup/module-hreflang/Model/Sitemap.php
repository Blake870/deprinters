<?php

namespace Swissup\Hreflang\Model;

use Magento\Framework\DataObject;

class Sitemap extends \Magento\Sitemap\Model\Sitemap
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->hreflangData = $this->getData('hreflangData');
        parent::_construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSitemapItems()
    {
        parent::_initSitemapItems();
        $this->hreflangData->injectXhtmlLinkSpecification($this->_tags);
    }

    /**
     * {@inheritdoc}
     */
    protected function _getSitemapRow($url, $lastmod = null, $changefreq = null, $priority = null, $images = null)
    {
        $xml = parent::_getSitemapRow($url, $lastmod, $changefreq, $priority, $images);
        $xml = $this->hreflangData->insertHreflag($xml, $url, $this->getStoreId());
        return $xml;
    }
}

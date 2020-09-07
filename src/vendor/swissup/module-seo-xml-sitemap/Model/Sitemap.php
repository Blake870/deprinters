<?php

namespace Swissup\SeoXmlSitemap\Model;

use Magento\Framework\DataObject;

class Sitemap extends \Magento\Sitemap\Model\Sitemap
{
    /**
     * Other Links model factory
     *
     * @var \Swissup\SeoHtmlSitemap\Model\LinkFactory
     */
    protected $otherFactory;

    /**
     * @var \Swissup\Hreflang\Helper\Sitemap
     */
    protected $hreflangData;

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->otherFactory = $this->getData('otherFactory');
        $this->hreflangData = $this->getData('hreflangData');
        parent::_construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSitemapItems()
    {
        parent::_initSitemapItems();
        /** @var $helper \Swissup\SeoXmlSitemap\Helper\Data */
        $helper = $this->_sitemapData;
        $storeId = $this->getStoreId();
        if ($this->otherFactory) {
            $collection = $this->otherFactory->create()
                ->getCollection()
                ->addStoreFilter($storeId);
            $this->addSitemapItem(new DataObject(
                [
                    'changefreq' => $helper->getOtherChangefreq($storeId),
                    'priority' => $helper->getOtherPriority($storeId),
                    'collection' => $collection,
                ]
            ));
        }

        $this->orderSitemapItemsByPriority();

        /* Swissup Hreflang intergartion */
        if ($this->hreflangData) {
            $this->hreflangData->injectXhtmlLinkSpecification($this->_tags);
        }
    }

    /**
     * @param  mixed $itemA
     * @param  mixed $itemB
     * @return boolean
     */
    protected function compareSitemapItems($itemA, $itemB)
    {
        if (!method_exists($itemA, 'getPriority')
            || !method_exists($itemB, 'getPriority')
        ) {
            return -1;
        }

        return ($itemB->getPriority() > $itemA->getPriority()) ? 1 : -1;
    }

    /**
     * Order Sitemap items by priority
     *
     * @return $this
     */
    protected function orderSitemapItemsByPriority()
    {
        $callback = [$this, 'compareSitemapItems'];
        usort($this->_sitemapItems, $callback);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _getSitemapRow($url, $lastmod = null, $changefreq = null, $priority = null, $images = null)
    {
        $xml = parent::_getSitemapRow($url, $lastmod, $changefreq, $priority, $images);
        /* Swissup Hreflang intergartion */
        if ($this->hreflangData) {
            $xml = $this->hreflangData->insertHreflag($xml, $url, $this->getStoreId());
        }

        return $xml;
    }

    /**
     * Add a sitemap item to the array of sitemap items
     *
     * @param DataObject $sitemapItem
     * @return $this
     * @since 100.2.0
     */
    public function addSitemapItem(DataObject $sitemapItem)
    {
        if (method_exists(\Magento\Sitemap\Model\Sitemap::class, 'addSitemapItem')) {
            return parent::addSitemapItem($sitemapItem);
        }

        $this->_sitemapItems[] = $sitemapItem;
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * Compatibility with M2.1.x
     */
    protected function _getMediaUrl($url)
    {
        if (strpos($url, 'http') === 0) {
            return $url;
        }

        return parent::_getMediaUrl($url);
    }
}

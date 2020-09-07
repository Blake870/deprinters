<?php

namespace Swissup\Hreflang\Helper;

use Magento\Sitemap\Model\Sitemap as XmlSitemap;

class Sitemap
{
    /**
     * @var array
     */
    protected $items;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface         $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->items = [];
    }

    /**
     * @param int                           $storeId
     * @param string                        $url
     * @param \Magento\Framework\DataObject $item
     */
    public function addItem($storeId, $url, \Magento\Framework\DataObject $item)
    {
        $this->items[$storeId . '::' . $url] = $item;
        return $this;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param  int                           $storeId
     * @param  string                        $url
     * @return \Magento\Framework\DataObject
     */
    public function getItem($storeId, $url)
    {
        $key = $storeId . '::' . $url;
        return isset($this->items[$key]) ? $this->items[$key] : null;
    }

    /**
     * @return $this
     */
    public function cleanItems()
    {
        $this->items = [];
        return $this;
    }

    /**
     * Specify the xhtml namespace
     *
     * @param  array  &$sitemapTags
     */
    public function injectXhtmlLinkSpecification(array &$sitemapTags)
    {
        // Hreflang requires xhtml namespace specification as it is below.
        // https://support.google.com/webmasters/answer/189077?hl=en
        $xhtmlLinkDef = ' xmlns:xhtml="http://www.w3.org/1999/xhtml"';
        // But it breaks XML preview in browsers.
        // For testing pursopes uncoment like below.
        // https://productforums.google.com/forum/#!topic/webmasters/0hxIjDJRZNc
        // $xhtmlLinkDef = ' xmlns:xhtml="http://www.w3.org/TR/xhtml11/xhtml11_schema.html"';
        $openTagKey = $sitemapTags[XmlSitemap::TYPE_URL][XmlSitemap::OPEN_TAG_KEY];
        $sitemapTags[XmlSitemap::TYPE_URL][XmlSitemap::OPEN_TAG_KEY] =
            str_replace(
                ' xmlns:image=',
                $xhtmlLinkDef .' xmlns:image=',
                $openTagKey
            );
    }

    /**
     * Insert hreflang links into xml row for
     *
     * @param  string $xmlRow
     * @param  string $url
     * @param  int    $storeId
     * @return string
     */
    public function insertHreflag($xmlRow, $url, $storeId)
    {
        $hreflang = $this->getItem($storeId, $url);
        if ($hreflang && $hreflang->hasData('collection')) {
            $extraXml = '';
            foreach ($hreflang->getCollection() as $lang => $href) {
                $extraXml .= "<xhtml:link rel=\"alternate\" hreflang=\"{$lang}\" href=\"{$href}\" />";
            }

            $xmlRow = str_replace('</url>', $extraXml . '</url>', $xmlRow);
        }

        return $xmlRow;
    }
}

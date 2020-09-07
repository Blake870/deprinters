<?php

namespace Swissup\SeoUrls\Plugin\LayeredNavigation\Block\Navigation;

use Magento\Catalog\Model\Layer\Filter\FilterInterface;
use Magento\Framework\View\Element\Template;

abstract class AbstractFilterRenderer
{
    /**
     * @var \Swissup\SeoUrls\Helper\Data
     */
    protected $helper;

    /**
     * @var \Swissup\SeoUrls\Model\Attribute
     */
    protected $seoAttribute;

    /**
     * @param \Swissup\SeoUrls\Helper\Data     $helper
     * @param \Swissup\SeoUrls\Model\Attribute $seoAttribute
     */
    public function __construct(
        \Swissup\SeoUrls\Helper\Data $helper,
        \Swissup\SeoUrls\Model\Attribute $seoAttribute
    ) {
        $this->helper = $helper;
        $this->seoAttribute = $seoAttribute;
    }

    public function addRelNofollowToLinks(
        FilterInterface $filter,
        Template $block,
        $html
    ) {
        try {
            $attribute = $filter->getAttributeModel();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $attribute = null;
        }

        if ($attribute
            && $this->helper->isSeoUrlsEnabled()
            && $this->seoAttribute->isNofollow($attribute)
        ) {
            $search = [];
            $replace = [];
            foreach ($filter->getItems() as $filterItem) {
                $url = $filterItem->getActionUrl(); // Swissup ALN uses this method
                if (!$url) {
                    $url = $filterItem->getUrl(); // Magento LN uses this method
                }

                $escapedUrl = $block->escapeUrl($url);
                $search[] = 'a href="' . $escapedUrl . '"';
                $replace[] = 'a href="' . $escapedUrl . '" rel="nofollow"';
            }

            $html = ($search && $replace)
                ? str_replace($search, $replace, $html)
                : $html;
        }

        return $html;

    }
}

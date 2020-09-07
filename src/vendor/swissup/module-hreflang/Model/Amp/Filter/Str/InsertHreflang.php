<?php

namespace Swissup\Hreflang\Model\Amp\Filter\Str;

use Magento\Framework\View;

class InsertHreflang
{
    /**
     * @var View\LayoutInterface
     */
    protected $layout;

    /**
     * @param View\LayoutInterface $layout
     */
    public function __construct(
        View\LayoutInterface $layout
    ) {
        $this->layout = $layout;
    }

    /**
     * Add hreflangs into head of AMP variant of page
     *
     * @param  string $html
     * @return string
     */
    public function process($html)
    {
        $find = '<link rel="canonical"';
        $replace = $this->getHreflangHtml() . $find;
        return str_replace($find, $replace, $html);
    }

    /**
     * Render hreflang links
     *
     * @param  string $html
     * @return string
     */
    protected function getHreflangHtml()
    {
        $html = '';
        $block = $this->layout->getBlock('hreflang.head.links');
        $hreflangs = $block ? $block->getHreflangs() : [];
        foreach ($hreflangs as $lang => $href) {
            $html .= "<link rel=\"alternate\" hreflang=\"{$lang}\" href=\"{$href}\" />";
        }

        return $html;
    }
}

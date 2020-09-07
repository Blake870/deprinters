<?php

namespace Swissup\SeoUrls\Plugin\LayeredNavigation\Block\Navigation;

use Magento\Catalog\Model\Layer\Filter\FilterInterface;
use Magento\Framework\View\Element\Template;

class FilterRenderer extends AbstractFilterRenderer
{
    /**
     * @var FilterInterface
     */
    private $filter;

    /**
     * Plugin to catch filter
     * (added for Magento 2.1.x support)
     *
     * @param  Template        $subject
     * @param  FilterInterface $filter
     */
    public function beforeRender(Template $subject, FilterInterface $filter)
    {
        $this->filter = $filter;
    }

    /**
     * Plugin to add rel="nofollow" into rendered links
     *
     * @param  Template $subject
     * @param  string   $result
     * @return string
     */
    public function afterRender(Template $subject, $result)
    {
        return $this->addRelNofollowToLinks($this->filter, $subject, $result);
    }
}

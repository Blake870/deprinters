<?php

namespace Swissup\SeoPager\Block;

use Swissup\SeoPager\Model\Config\Source\Strategy;

class Head extends \Magento\Framework\View\Element\Template
{
    /**
     * @param \Swissup\SeoPager\Helper\Data                    $helper
     * @param \Swissup\SeoPager\Model\ToolbarResolver          $toolbarResolver
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array                                            $data
     */
    public function __construct(
        \Swissup\SeoPager\Helper\Data $helper,
        \Swissup\SeoPager\Model\ToolbarResolver $toolbarResolver,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->toolbarResolver = $toolbarResolver;
        return parent::__construct($context, $data);
    }

    /**
     * Get listing pager block
     *
     * @return null|Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    public function getToolbar()
    {
        return $this->toolbarResolver->getToolbarBlock();
    }

    /**
     * Before rendering html (check if toolbar found)
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        if (($this->helper->getPresentationStrategy() == Strategy::LEAVE_AS_IS)
            || !$this->getToolbar()
        ) {
            $this->setTemplate('');
        }

        return parent::_beforeToHtml();
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        if ($this->canUseCanonical() || $this->canUsePrevNext())
        {
            // remove Magento generated rel="canonical"
            foreach ($this->pageConfig->getAssetCollection()->getGroups() as $group) {
                if ($group->getProperty('content_type') == 'canonical') {
                    $assetIdentifiers = array_keys($group->getAll());
                    foreach ($assetIdentifiers as $identifier) {
                        $group->remove($identifier);
                    }
                }
            }
        }

        return parent::_prepareLayout();
    }

    /**
     * Check if rel="canonical" is allowed
     *
     * @return bool
     */
    public function canUseCanonical()
    {
        return $this->helper->getPresentationStrategy() == Strategy::REL_CANONICAL;
    }

    /**
     * Check if rel="prev" and rel="next" is allowed
     *
     * @return bool
     */
    public function canUsePrevNext()
    {
        return $this->helper->getPresentationStrategy() == Strategy::REL_NEXT_REL_PREV;
    }

    /**
     * Get next page url
     *
     * @return string
     */
    public function getNextPageUrl()
    {
        $pager = $this->getLayout()->getBlock('product_list_toolbar_pager');
        if ($pager) {
            return $pager->getPageUrl($this->getToolbar()->getCurrentPage()+1);
        }

        return '';
    }

    /**
     * Get previous page url
     *
     * @return string
     */
    public function getPreviousPageUrl()
    {
        $pager = $this->getLayout()->getBlock('product_list_toolbar_pager');
        if ($pager) {
            return $this->getToolbar()->getCurrentPage() > 2
                ? $pager->getPageUrl($this->getToolbar()->getCurrentPage()-1)
                : $pager->getPageUrl(null);
        }

        return '';
    }

    /**
     * Check if current page is a fisrt page
     *
     * @return bool
     */
    public function isFirstPage()
    {
        return $this->getToolbar()->isFirstPage();
    }

    /**
     * Check if current page is a last page
     *
     * @return bool
     */
    public function isLastPage()
    {
        return $this->getToolbar()->getCurrentPage() >= $this->getToolbar()->getLastPageNum();
    }

    /**
     * Get view-all page URL
     *
     * @return string
     */
    public function getViewAllPageUrl()
    {
        return $this->helper->getViewAllPageUrl(false);
    }

}

<?php

namespace Swissup\SeoPager\Model;

class ToolbarResolver
{
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;

    /**
     * @var array
     */
    protected $layoutBlockName;

    /**
     * @var \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    protected $toolbarBlock;

    /**
     *  Parameter $layoutBlockName supplied via di.xml.
     *
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param array                                   $layoutBlockName
     */
    public function __construct(
        \Magento\Framework\View\LayoutInterface $layout,
        array $layoutBlockName = []
    ) {
        $this->layout = $layout;
        $this->layoutBlockName = $layoutBlockName;
    }

    /**
     * Get toolbar block for current page
     *
     * @return \Magento\Catalog\Block\Product\ProductList\Toolbar|null
     */
    public function getToolbarBlock()
    {
        if (!$this->toolbarBlock) {
            $this->toolbarBlock = $this->_findToolbarBlock();
        }

        return $this->toolbarBlock;
    }

    /**
     * Find out what is toolbar for current page
     *
     * @return \Magento\Catalog\Block\Product\ProductList\Toolbar|null
     */
    private function _findToolbarBlock()
    {
        foreach ($this->layoutBlockName as $blockName) {
            $listing = $this->layout->getBlock($blockName);
            if (!$listing) {
                continue;
            }

            $toolbar = $listing->getToolbarBlock();
            if (!$toolbar) {
                continue;
            }

            if (!$toolbar->getCollection()) {
                $collection = $listing->getLoadedProductCollection();
                $toolbar->setCollection($collection);
            }

            return $toolbar;
        }

        return null;
    }
}

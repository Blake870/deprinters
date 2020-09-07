<?php
namespace Swissup\Lightboxpro\Plugin;

class SetGalleryImageSize
{
    /**
     * @var \Swissup\Lightboxpro\Helper\Config
     */
    protected $helper;

    /**
     * @param \Swissup\Lightboxpro\Helper\Config $helper
     */
    public function __construct(
        \Swissup\Lightboxpro\Helper\Config $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Catalog\Block\Product\View\Gallery $subject
     * @param callable $proceed
     * @return \Magento\Framework\Data\Collection
     */
    public function aroundGetGalleryImages(
        \Magento\Catalog\Block\Product\View\Gallery $subject,
        callable $proceed
    ) {
        $product = $subject->getProduct();

        return $this->helper->getGalleryImages($product);
    }
}

<?php
namespace Swissup\Lightboxpro\Plugin;

class SetConfigurableImageSize
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
     * @param \Magento\ConfigurableProduct\Helper\Data $subject
     * @param callable $proceed
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return \Magento\Framework\Data\Collection
     */
    public function aroundGetGalleryImages(
        \Magento\ConfigurableProduct\Helper\Data $subject,
        callable $proceed,
        \Magento\Catalog\Api\Data\ProductInterface $product
    ) {
        return $this->helper->getGalleryImages($product);
    }
}

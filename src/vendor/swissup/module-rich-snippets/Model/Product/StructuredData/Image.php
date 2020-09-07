<?php

namespace Swissup\RichSnippets\Model\Product\StructuredData;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\NotFoundException;

class Image
{
    /**
     * @var ProductInterface
     */
    protected $product;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;

    /**
     * @param ProductInterface              $product
     * @param \Magento\Catalog\Helper\Image $imageHelper
     */
    public function __construct(
        ProductInterface $product,
        \Magento\Catalog\Helper\Image $imageHelper
    ) {
        $this->product = $product;
        $this->imageHelper = $imageHelper;
    }

    /**
     * Get 'image' for product structured data
     *
     * @return string
     */
    public function get()
    {
        if (!$this->product->getId()) {
            throw new NotFoundException(__('Product not found.'));
        }

        return $this->imageHelper
            ->init($this->product, 'product_page_image_small')
            ->setImageFile($this->product->getImage())
            ->getUrl();;
    }
}

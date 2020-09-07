<?php

namespace Swissup\SeoTemplates\Plugin\Block\Product;

use Swissup\SeoTemplates\Model\SeodataBuilder;
use Swissup\SeoTemplates\Model\Template;
use Magento\Catalog\Block\Product as ProductBlock;
use Magento\Catalog\Model\Product;

class ImageBuilder
{
    /**
     * @var Product
     */
    private $product;

    /**
     * @param SeodataBuilder $seodataBuilder
     */
    public function __construct(
        SeodataBuilder $seodataBuilder,
        \Swissup\SeoTemplates\Helper\Data $helper
    ) {
        $this->seodataBuilder = $seodataBuilder;
        $this->helper = $helper;
    }

    /**
     * Plugin before for method `setProduct`.
     * Catch product at old Magento when method `create` had no params.
     *
     * @param  ProductBlock\ImageBuilder $subject [description]
     * @param  Product                   $product [description]
     */
    public function beforeSetProduct(
        ProductBlock\ImageBuilder $subject,
        Product $product
    ) {
        $this->product = $product;

        return null;
    }

    /**
     * Plugin after for method `create`
     * @param  ProductBlock\ImageBuilder $subject
     * @param  ProductBlock\Image        $result
     * @param  Product|null              $product
     * @return ProductBlock\Image
     */
    public function afterCreate(
        ProductBlock\ImageBuilder $subject,
        ProductBlock\Image $result,
        Product $product = null
    ) {
        $product = $product ?? $this->product;
        if (!$product || !$this->helper->isEnabled()) {
            return $result;
        }

        $seodata = $this->seodataBuilder->get(
            $product->getId(),
            Template::ENTITY_TYPE_PRODUCT
        );
        if ($seodata->getImageAlt()
            && (
                $result->getLabel() === $product->getName()
                || $this->helper->isForced()
            )
        ) {
            $result->setLabel($seodata->getImageAlt());
        }

        return $result;
    }
}

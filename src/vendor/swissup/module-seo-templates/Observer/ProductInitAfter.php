<?php

namespace Swissup\SeoTemplates\Observer;

use Swissup\SeoTemplates\Model\Template;

class ProductInitAfter extends AbstractInitAfter
{
    /**
     * {@inheritdoc}
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->helper->isEnabled()) {
            $product = $observer->getEvent()->getProduct();
            $this->updateMetadata($product, Template::ENTITY_TYPE_PRODUCT);
            // Workaround to set meta keywords for product.
            // Check code at \Magento\Catalog\Helper\Product\View::preparePageMetadata#L120
            // Category works normal!
            if ($product->hasMetaKeywords()) {
                $product->setData('meta_keyword', $product->getMetaKeywords());
            }

            $this->optimizeMetadata($product);
            $this->updateMediaGallery($product, Template::ENTITY_TYPE_PRODUCT);
        }

        return $this;
    }

    /**
     * @param  \Magento\Catalog\Model\Product $product
     * @param  string                         $entityType
     */
    protected function updateMediaGallery(
        \Magento\Catalog\Model\Product $product,
        $entityType
    ) {
        $mediaGallery = $product->getMediaGallery();
        if (!isset($mediaGallery['images'])) {
            return;
        }

        $images = &$mediaGallery['images'];
        if (!is_array($images)) {
            return;
        }

        $seodata = $this->seodataBuilder->get($product->getId(), $entityType);
        foreach ($images as &$image) {
            if ($seodata->getImageAlt()    // there is generated image alt
                && (
                    empty($image['label']) // AND image label is empty
                    || $this->helper->isForced()   // or force generated data enabled
                )
            ) {
                $image['label'] = $seodata->getImageAlt();
            }
        }

        $product->setMediaGallery($mediaGallery);
    }
}

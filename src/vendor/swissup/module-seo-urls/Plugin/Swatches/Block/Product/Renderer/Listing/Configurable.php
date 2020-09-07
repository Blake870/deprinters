<?php

namespace Swissup\SeoUrls\Plugin\Swatches\Block\Product\Renderer\Listing;

class Configurable
{
    /**
     * @var \Swissup\SeoUrls\Helper\Data
     */
    private $helper;

    /**
     * @var \Swissup\SeoUrls\Model\Attribute
     */
    private $seoAttribute;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    private $encoder;

    /**
     * @var \Magento\Framework\Json\DecoderInterface
     */
    private $decoder;

    /**
     * @param \Swissup\SeoUrls\Helper\Data $helper
     */
    public function __construct(
        \Swissup\SeoUrls\Helper\Data $helper,
        \Swissup\SeoUrls\Model\Attribute $seoAttribute,
        \Magento\Framework\Json\EncoderInterface $encoder,
        \Magento\Framework\Json\DecoderInterface $decoder
    ) {
        $this->helper = $helper;
        $this->seoAttribute = $seoAttribute;
        $this->encoder = $encoder;
        $this->decoder = $decoder;
    }

    public function afterGetJsonSwatchConfig(
        \Magento\Swatches\Block\Product\Renderer\Listing\Configurable $subject,
        $result
    ) {
        if ($this->helper->isSeoUrlsEnabled()
            && $config = $this->decoder->decode($result)
        ) {
            foreach ($config as $swatchId => &$swatch) {
                $product = $subject->getProduct();
                $attribute = $product->getResource()->getAttribute($swatchId);
                // set in-URL label for attribute
                $swatch['inUrlLabel'] = $this->seoAttribute->getStoreLabel($attribute);
                foreach ($swatch as $itemId => &$item) {
                    if (!is_array($item)) {
                        continue;
                    }

                    $option = new \Magento\Framework\DataObject([
                        'value' => $itemId,
                        'label' => isset($item['label']) ? $item['label'] : ''
                    ]);
                    // set in-URL value for attribute
                    $item['inUrlValue'] = $this->seoAttribute->getStoreValue(
                        $attribute,
                        $option
                    );
                }

            }

            $result = $this->encoder->encode($config);
        }

        return $result;
    }
}

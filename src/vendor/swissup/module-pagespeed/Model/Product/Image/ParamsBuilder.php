<?php

namespace Swissup\Pagespeed\Model\Product\Image;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\ConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Builds parameters array used to build Image Asset
 */
class ParamsBuilder
{
    /**
     * @var int
     */
    protected $defaultQuality = 80;

    /**
     * @var array
     */
    protected $defaultBackground = [255, 255, 255];

    /**
     * @var int|null
     */
    protected $defaultAngle = null;

    /**
     * @var bool
     */
    protected $defaultKeepAspectRatio = true;

    /**
     * @var bool
     */
    protected $defaultKeepTransparency = true;

    /**
     * @var bool
     */
    protected $defaultConstrainOnly = true;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var ConfigInterface
     */
    protected $viewConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ConfigInterface $viewConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ConfigInterface $viewConfig
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->viewConfig = $viewConfig;
    }

    /**
     * @param array $imageArguments
     * @return array
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function build(array $imageArguments): array
    {
        $miscParams = [
            'image_type' => $imageArguments['type'] ?? null,
            'image_height' => $imageArguments['height'] ?? null,
            'image_width' => $imageArguments['width'] ?? null,
        ];

        $overwritten = $this->overwriteDefaultValues($imageArguments);
        $watermark = isset($miscParams['image_type']) ? $this->getWatermark($miscParams['image_type']) : [];

        return array_merge($miscParams, $overwritten, $watermark);
    }

    /**
     * @param array $imageArguments
     * @return array
     */
    protected function overwriteDefaultValues(array $imageArguments): array
    {
        $frame = $imageArguments['frame'] ?? $this->hasDefaultFrame();
        $constrain = $imageArguments['constrain'] ?? $this->defaultConstrainOnly;
        $aspectRatio = $imageArguments['aspect_ratio'] ?? $this->defaultKeepAspectRatio;
        $transparency = $imageArguments['transparency'] ?? $this->defaultKeepTransparency;
        $background = $imageArguments['background'] ?? $this->defaultBackground;
        $angle = $imageArguments['angle'] ?? $this->defaultAngle;

        return [
            'background' => (array) $background,
            'angle' => $angle,
            'quality' => $this->defaultQuality,
            'keep_aspect_ratio' => (bool) $aspectRatio,
            'keep_frame' => (bool) $frame,
            'keep_transparency' => (bool) $transparency,
            'constrain_only' => (bool) $constrain,
        ];
    }

    /**
     * @param string $type
     * @return array
     */
    protected function getWatermark(string $type): array
    {
        $file = $this->scopeConfig->getValue(
            "design/watermark/{$type}_image",
            ScopeInterface::SCOPE_STORE
        );

        if ($file) {
            $size = $this->scopeConfig->getValue(
                "design/watermark/{$type}_size",
                ScopeInterface::SCOPE_STORE
            );
            $opacity = $this->scopeConfig->getValue(
                "design/watermark/{$type}_imageOpacity",
                ScopeInterface::SCOPE_STORE
            );
            $position = $this->scopeConfig->getValue(
                "design/watermark/{$type}_position",
                ScopeInterface::SCOPE_STORE
            );
            $width = !empty($size['width']) ? $size['width'] : null;
            $height = !empty($size['height']) ? $size['height'] : null;

            return [
                'watermark_file' => $file,
                'watermark_image_opacity' => $opacity,
                'watermark_position' => $position,
                'watermark_width' => $width,
                'watermark_height' => $height
            ];
        }

        return [];
    }

    /**
     * Get frame from product_image_white_borders
     * @return bool
     */
    protected function hasDefaultFrame(): bool
    {
        return (bool) $this->viewConfig->getViewConfig()->getVarValue(
            'Magento_Catalog',
            'product_image_white_borders'
        );
    }
}

<?php
namespace Swissup\Lightboxpro\Model\Config\Source;

class ThumbnailsTypes implements \Magento\Framework\Data\OptionSourceInterface
{
    const TYPE_THEME = 'theme';
    const TYPE_HORIZONTAL = 'horizontal';
    const TYPE_VERTICAL = 'vertical';
    const TYPE_HIDDEN = 'hidden';

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::TYPE_THEME, 'label' => __('Theme Defined')],
            ['value' => self::TYPE_HORIZONTAL, 'label' => __('Horizontal')],
            ['value' => self::TYPE_VERTICAL, 'label' => __('Vertical')],
            ['value' => self::TYPE_HIDDEN, 'label' => __('Hidden')]
        ];
    }
}

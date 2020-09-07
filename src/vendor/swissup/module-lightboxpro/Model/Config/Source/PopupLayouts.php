<?php
namespace Swissup\Lightboxpro\Model\Config\Source;

class PopupLayouts implements \Magento\Framework\Data\OptionSourceInterface
{
    const TYPE_DEFAULT = 'default';
    const TYPE_SIMPLE = 'simple';
    const TYPE_ADVANCED = 'advanced';

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::TYPE_DEFAULT, 'label' => __('Default')],
            ['value' => self::TYPE_SIMPLE, 'label' => __('Simple Lightbox')],
            ['value' => self::TYPE_ADVANCED, 'label' => __('Advanced Lightbox')]
        ];
    }
}

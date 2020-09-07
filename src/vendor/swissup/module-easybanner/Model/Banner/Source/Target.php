<?php
namespace Swissup\Easybanner\Model\Banner\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Target implements OptionSourceInterface
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            ['label' => __('Popup Window'), 'value' => 'popup'],
            ['label' => __('New Window'), 'value' => 'blank'],
            ['label' => __('Same Window'), 'value' => 'self'],
        ];

        return $options;
    }
}

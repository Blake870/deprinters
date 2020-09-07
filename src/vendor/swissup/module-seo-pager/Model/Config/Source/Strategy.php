<?php

namespace Swissup\SeoPager\Model\Config\Source;

class Strategy implements \Magento\Framework\Option\ArrayInterface
{

    const LEAVE_AS_IS = 0;

    const REL_CANONICAL = 1;

    const REL_NEXT_REL_PREV = 2;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::LEAVE_AS_IS,
                'label' => __('Leave as-is')
            ],
            [
                'value' => self::REL_CANONICAL,
                'label' => __('Use rel="canonical" to view all page')
            ],
            [
                'value' => self::REL_NEXT_REL_PREV,
                'label' => __('Use rel="next" and rel="prev" (deprecated)')
            ]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $array = [];
        foreach ($this->toOptionArray() as $option) {
            $array[$option['value']] = $optin['label'];
        }

        return $array;
    }
}

<?php

namespace Swissup\Highlight\Model\Config\Source;

class Period implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'P1M',  'label' => __('1 Month')],
            ['value' => 'P1Y',  'label' => __('1 Year')],
            ['value' => 'P6M',  'label' => __('6 Months')],
            ['value' => 'P7D',  'label' => __('1 Week')],
            ['value' => 'P1D',  'label' => __('1 Day')],
            ['value' => 'PT1H', 'label' => __('1 Hour')],
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $result = array();
        foreach ($this->toOptionArray() as $values) {
            $result[$values['value']] = $values['label'];
        }
        return $result;
    }
}

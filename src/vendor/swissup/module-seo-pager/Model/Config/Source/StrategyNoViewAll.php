<?php

namespace Swissup\SeoPager\Model\Config\Source;

class StrategyNoViewAll extends Strategy
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => parent::LEAVE_AS_IS,
                'label' => __('Leave as-is')
            ],
            [
                'value' => parent::REL_NEXT_REL_PREV,
                'label' => __('Use rel="next" and rel="prev" (deprecated)')
            ]
        ];
    }
}

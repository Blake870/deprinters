<?php

namespace Swissup\Highlight\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Remove all forbidden data parameters
     *
     * @param  array $data
     * @return array
     */
    public function filterBlockData(array $data)
    {
        $filtered = array_filter(
            $data,
            [$this, 'isAllowedBlockData'],
            ARRAY_FILTER_USE_KEY
        );

        if (isset($filtered['type'])) {
            if (strpos($filtered['type'], 'Swissup\Highlight') !== 0) {
                unset($filtered['type']);
            } else {
                $filtered['type'] = str_replace('\Interceptor', '', $filtered['type']);
            }
        }

        return $filtered;
    }

    /**
     * Check if data parameter is allowed
     *
     * @param  string  $key
     * @return boolean      [description]
     */
    public function isAllowedBlockData($key)
    {
        $whitelist = array(
            'attribute_code',
            'carousel',
            'column_count',
            'conditions_encoded',
            'category_ids',
            'dir',
            'min_popularity',
            'mode',
            'order',
            'page_count',
            'page_var_name',
            'period',
            'products_count',
            'template',
            'type'
        );
        return in_array($key, $whitelist);
    }


    /**
     * Get data to initialize Slick Carousel
     *
     * @param  \Swissup\Highlight\Block\ProductList\All $block
     * @param  string $dataSourceUrl [description]
     * @param  string $format        [description]
     * @return string|array
     */
    public function getSlickCarouselData(
        $block,
        $dataSourceUrl = '',
        $format = 'json',
        $isRtl = false
    ) {
        $blockData = $block->getData();
        // compatibility with `Content > Widgets` created widgets
        if (empty($blockData['template'])) {
            $blockData['template'] = $block->getTemplate();
        }

        // minimize params count
        if (isset($blockData['conditions_encoded'])) {
            $conditions = $block->getConditionsDecoded();
            if (!count($conditions) || count($conditions) === 1) {
                unset($blockData['conditions_encoded']);
            }
        }

        $slickParams = [
            'infinite' => false,
            'swipeToSlide' => true,
            'arrows' => true,
            'rtl' => $isRtl,
            // 'dots' => $block->showPager(), // slick does not support dots for ajax loaded slides
            'dataSourceUrl' => $dataSourceUrl ? $dataSourceUrl : $this->_getUrl('highlight/carousel/slide'),
            'blockData' => $this->filterBlockData($blockData)
        ];

        // disable carousel on small screens
        if (isset($blockData['css_class']) &&
            strpos($blockData['css_class'], 'hl-magazine') !== false
        ) {
            // @see highliht.less
            $slickParams['responsive'] = [
                [
                    'breakpoint' => 480,
                    'settings' => 'unslick',
                ]
            ];
        }

        if ($format == 'json') {
            return json_encode($slickParams, JSON_HEX_APOS);
        }

        return $slickParams;
    }
}

<?php

namespace Swissup\SeoUrls\Model\Layer;

class PredefinedFilters extends \Magento\Framework\DataObject
{
    /**
     * Construct
     *
     * @param \Magento\Framework\DataObject\Factory              $dataObjectFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\DataObject\Factory $dataObjectFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        // define category filter
        if (!isset($data['category_filter'])) {
            // value 'cat' is hardcoded in \Magento\CatalogSearch\Model\Layer\Filter\Category
            $data['category_filter'] = $dataObjectFactory->create(
                [
                    'request_var' => 'cat',
                    'store_label' => $scopeConfig->getValue(
                        'swissup_seourls/layered_navigation/category_label',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    )
                ]
            );
        }

        // define stock filter
        if (!isset($data['stock_filter'])) {
            // value 'cat' is hardcoded in \Swissup\Ajaxlayerednavigation\Model\Layer\Filter\Stock
            $data['stock_filter'] = $dataObjectFactory->create(
                [
                    'request_var' => 'in-stock',
                    'store_label' => __('Stock'),
                    'attribute_code' => 'quantity_and_stock_status'
                ]
            );
        }

        // define stock filter
        if (!isset($data['rating_filter'])) {
            // value 'rating' is hardcoded in \Swissup\Ajaxlayerednavigation\Model\Layer\Filter\Rating
            $data['rating_filter'] = $dataObjectFactory->create(
                [
                    'request_var' => 'rating',
                    'store_label' => __('Rating'),
                    'attribute_code' => 'swissup_rating_summary'
                ]
            );
        }

        parent::__construct($data);
    }
}

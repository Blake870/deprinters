<?php

namespace Swissup\RichSnippets\Model\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Store\Model\ScopeInterface;

class StructuredData extends StructuredData\AbstractData
{
    /**
     * @param \Magento\Store\Model\StoreManagerInterface                $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface        $scopeConfig
     * @param \Swissup\RichSnippets\Model\Config\Backend\StructuredData $configValueProcessor
     * @param array                                                     $dataSnippetFactory
     * @param \Magento\Catalog\Helper\Output                            $attributeOutput
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Swissup\RichSnippets\Model\Config\Backend\StructuredData $configValueProcessor,
        array $dataSnippetFactory = [],
        \Magento\Catalog\Helper\Output $attributeOutput
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->configValueProcessor = $configValueProcessor;
        $this->dataSnippetFactory = $dataSnippetFactory;
        parent::__construct($attributeOutput);
    }

    /**
     * Get structured data for product
     *
     * @param  ProductInterface $product
     * @return array
     */
    public function get(ProductInterface $product)
    {
        $data = [
            '@context'    => 'http://schema.org',
            '@type'       => 'Product'
        ];

        $dataMap = $this->getDataMap($product->getStoreId());
        // data from config map
        $data += $this->buildAttributeBasedData($dataMap, $product);

        // predefined data snippets
        foreach ($this->dataSnippetFactory as $property => $factory) {
            $dataSnippet = $factory->create(['product' => $product]);
            if (!isset($data[$property]) || empty($data[$property])) {
                $additionalMap = isset($dataMap[$property]) && is_array($dataMap[$property])
                    ? $dataMap[$property]
                    : [];
                $data[$property] = $dataSnippet->get($additionalMap);
            }
        }

        return $data;
    }

    /**
     * Get snippet data map
     *
     * @param  int $storeId
     * @return array
     */
    public function getDataMap($storeId)
    {
        $store = $this->storeManager->getStore($storeId);
        $configValue = $this->scopeConfig->getValue(
            'richsnippets/product/structured_data',
            ScopeInterface::SCOPE_STORE,
            $store
        );

        $arrayFieldValue = $this->configValueProcessor->makeArrayFieldValue($configValue);
        $dataMap = [];
        foreach ($arrayFieldValue as $item) {
            if (strpos($item['property'], '/') !== false) {
                $keys = explode('/', $item['property'], 2);
                $dataMap[$keys[0]][$keys[1]] = $item['product_attribute'];
            } else {
                $dataMap[$item['property']] = $item['product_attribute'];
            }
        }

        return $dataMap;
    }
}

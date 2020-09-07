<?php

namespace Swissup\RichSnippets\Model\Product\StructuredData;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Store\Model\ScopeInterface;
use Magento\ConfigurableProduct\Pricing\Price\LowestPriceOptionsProviderInterface;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Offers extends AbstractData
{
    /**
     * @var ProductInterface
     */
    protected $product;

    /**
     * @var LowestPriceOptionsProviderInterface
     */
    protected $lowestPriceProvider;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Configurable
     */
    protected $resourceProductConfigurable;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @param ProductInterface                           $product
     * @param LowestPriceOptionsProviderInterface        $lowestPriceProvider
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param ScopeConfigInterface                       $scopeConfig
     * @param Configurable                               $resourceProductConfigurable
     * @param ProductRepository                          $productRepository
     * @param SearchCriteriaBuilder                      $searchCriteriaBuilder
     * @param \Magento\Catalog\Helper\Output             $attributeOutput
     */
    public function __construct(
        ProductInterface $product,
        LowestPriceOptionsProviderInterface $lowestPriceProvider,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        Configurable $resourceProductConfigurable,
        ProductRepository $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Catalog\Helper\Output $attributeOutput
    ) {
        $this->product = $product;
        $this->lowestPriceProvider = $lowestPriceProvider;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->resourceProductConfigurable = $resourceProductConfigurable;
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        parent::__construct($attributeOutput);
    }

    /**
     * Get offers of product for Google Structured Data
     *
     * @param  array  $dataMap
     * @return array
     */
    public function get(array $dataMap = [])
    {
        if (!$this->product->getId()) {
            throw new NotFoundException(__('Product not found.'));
        }

        $store = $this->storeManager->getStore($this->product->getStoreId());
        $currency = $store->getCurrentCurrency();
        if ('configurable' === $this->product->getTypeId()) {
            $products = $this->getChildrenOfConfigurable($this->product);
            $dataMap += ['name' => 'name', 'sku' => 'sku'];
        } else {
            $products = [$this->product];
        }

        $offers = [];
        foreach ($products as $product) {
            $offer = $this->buildAttributeBasedData($dataMap, $product);
            $offer += [
                    '@type' => 'Offer',
                    'availability' => $this->getStockStatus($product),
                    'priceCurrency' => $currency->getCode(),
                    'priceValidUntil' => $this->getValidUntil($product),
                    'price' => $this->getPriceValues($product),
                    'url' => $this->product->getProductUrl(),
                    'itemCondition' => $this->getCondition($product)
                ];
            $offers[] = $offer;
        }

        return $offers;
    }

    /**
     * Get price
     *
     * @param  ProductInterface $product
     * @return float Lowest price for product
     */
    protected function getPriceValues(ProductInterface $product)
    {
        $priceModel  = $product->getPriceModel();
        $productType = $product->getTypeInstance();
        if ('bundle' === $product->getTypeId()) {
            return min($priceModel->getTotalPrices($product));
        }

        if ('grouped' === $product->getTypeId()) {
            $assocProducts = $productType->getAssociatedProductCollection($product)
                ->addMinimalPrice()
                ->setOrder('minimal_price', 'ASC');

            foreach ($assocProducts as $assocProduct) {
                $groupedProductsPricesArray[] = $assocProduct->getFinalPrice();
            }

            if (isset($groupedProductsPricesArray)) {
               return min($groupedProductsPricesArray);
            }
        }

        $minPrice   = $product->getMinimalPrice();
        $finalPrice = $product->getPriceInfo()->getPrice('final_price')
            ->getAmount()->getValue();
        if ($minPrice) {
            return min($minPrice, $finalPrice);
        }

        return $finalPrice;
    }

    /**
     * Get stock status for product
     *
     * @param  ProductInterface $product
     * @return string
     */
    protected function getStockStatus(ProductInterface $product)
    {
        $availability = 'http://schema.org/OutOfStock';
        if ($product->isSaleable()){
            $availability = 'http://schema.org/InStock';
        }

        return $availability;
    }

    /**
     * Get condition of product for Google Structured Data
     *
     * @param  ProductInterface $product
     * @return string
     */
    protected function getCondition(ProductInterface $product)
    {
        $itemCondition = 'http://schema.org/NewCondition';
        $store = $this->storeManager->getStore($product->getStoreId());
        $conditionAttributeCode = $this->scopeConfig->getValue(
            'richsnippets/product/condition_attribute',
            ScopeInterface::SCOPE_STORE,
            $store
        );
        $frontendValue = $this->getAttributeFrontendValue(
            $product,
            $conditionAttributeCode,
            true
        );
        if ($frontendValue) {
            foreach (['new', 'used', 'damaged', 'refurbished'] as $condition) {
                $configValue = $this->scopeConfig->getValue(
                    "richsnippets/product/condition_{$condition}_option",
                    ScopeInterface::SCOPE_STORE,
                    $store
                );
                if ($configValue == $frontendValue) {
                    $condition = ucfirst($condition);
                    $itemCondition = "http://schema.org/{$condition}Condition";
                    break;
                }
            }
        }

        return $itemCondition;
    }

    /**
     * Get attribute value for current product
     *
     * @param  ProductInterface $product
     * @param  string  $attributeCode
     * @param  boolean $defaultValue Flag to return default of store related
     * @return string|null
     */
    protected function getAttributeFrontendValue(
        ProductInterface $product,
        $attributeCode,
        $defaultValue = false
    ) {
        $value = $product->getData($attributeCode);
        if (!$attributeCode || !$value) {
            return null;
        }

        $attribute = $product->getResource()->getAttribute($attributeCode);
        /** @var options array Default (admin) labels */
        $options = $attribute->getSource()->getAllOptions(false, $defaultValue);
        foreach ($options as $option) {
            if (isset($option['value']) && $option['value'] == $value) {
                return isset($option['label']) ? $option['label'] : $option['value'];
            }
        }

        return null;
    }

    /**
     * Get valid until date for price
     *
     * @param  ProductInterface $product
     * @return string
     */
    protected function getValidUntil(ProductInterface $product)
    {
        $finalPrice = $product->getPriceInfo()->getPrice('final_price');
        $validUntil = '';
        if ('configurable' === $product->getTypeId()) {
            foreach ($this->lowestPriceProvider->getProducts($product) as $subProduct) {
                $specialPrice = $subProduct->getPriceInfo()->getPrice('special_price');
                if ($specialPrice->getValue() == $finalPrice->getValue()) {
                    $validUntil = max($specialPrice->getSpecialToDate(), $validUntil);
                }
            }
        } else {
            $specialPrice = $product->getPriceInfo()->getPrice('special_price');
            if ($specialPrice) {
                $validUntil = $specialPrice->getSpecialToDate();
            }
        }

        if (!$validUntil) {
            $store = $this->storeManager->getStore($product->getStoreId());
            $validUntil = $this->scopeConfig->getValue(
                'richsnippets/product/price_valid_until',
                ScopeInterface::SCOPE_STORE,
                $store
            );
        }

        return $validUntil;
    }

    /**
     * Get children of configurable product
     *
     * @param  ProductInterface $superProduct
     * @return array
     */
    public function getChildrenOfConfigurable(ProductInterface $superProduct)
    {
        $groups = $this->resourceProductConfigurable->getChildrenIds($superProduct->getId());
        $ids = [];
        foreach ($groups as $children) {
            $ids = array_merge($ids, $children);
        }

        if (empty($ids)) {
            return null;
        }

        $criteria = $this->searchCriteriaBuilder
            ->addFilter('entity_id', implode(',', $ids), 'in')
            ->create();

        return $this->productRepository->getList($criteria)->getItems();
    }
}

<?php

namespace Swissup\SoldTogether\Block;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Framework\DataObject\IdentityInterface;

class Order extends AbstractProduct implements IdentityInterface
{
    /**
     * Can be 'order' or 'customer'
     */
    const SOLDTOGETHER_ENTITY = 'order';

    /**
     *  Product collection
     *
     * @var Collection
     */
    protected $_collection;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $catalogProductVisibility;

    /**
     * @var \Swissup\SoldTogether\Helper\Stock
     */
    protected $stockHelper;

    /**
     * @var \Magento\Framework\Locale\FormatInterface
     */
    protected $localeFormat;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Swissup\SoldTogether\Model\ResourceModel\AbstractResourceModel
     */
    protected $resource;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Catalog\Model\Product\Visibility $catalogVisibility
     * @param \Swissup\SoldTogether\Helper\Stock $stockHelper
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Swissup\SoldTogether\Model\ResourceModel\AbstractResourceModel $resource
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Catalog\Model\Product\Visibility $catalogVisibility,
        \Swissup\SoldTogether\Helper\Stock $stockHelper,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Swissup\SoldTogether\Model\ResourceModel\AbstractResourceModel $resource,
        array $data = []
    ) {
        $this->httpContext = $httpContext;
        $this->moduleManager = $moduleManager;
        $this->catalogProductVisibility = $catalogVisibility;
        $this->stockHelper = $stockHelper;
        $this->localeFormat = $localeFormat;
        $this->collectionFactory = $collectionFactory;
        $this->categoryFactory = $categoryFactory;
        $this->checkoutSession = $checkoutSession;
        $this->resource = $resource;

        return parent::__construct($context, $data);
    }

    /**
     * Initialize block's cache
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->addData(
            [
                'cache_lifetime' => 86400,
                'cache_tags' => [\Magento\Catalog\Model\Product::CACHE_TAG]
            ]
        );
    }

    /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return [
            'SOLDTOGETHER_' . static::SOLDTOGETHER_ENTITY,
            $this->_storeManager->getStore()->getId(),
            $this->_design->getDesignTheme()->getId(),
            $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP),
            'template' => $this->getTemplate(),
            'name' => $this->getNameInLayout(),
            $this->getProductsCount(),
            implode(',', $this->getProductIds()),
        ];
    }

    /**
     * Get product ids that will be used to retrieve related products collection
     *
     * @return array
     */
    public function getProductIds()
    {
        $ids = [];

        if ($this->getProduct()) {
            $ids[] = $this->getProduct()->getId();
        } else {
            $items = $this->checkoutSession->getQuote()->getAllItems();
            foreach ($items as $item) {
                $ids[] = $item->getProductId();
            }
        }

        return $ids;
    }

    /**
     * Prepare product collection using soldtogether data
     *
     * @return $this
     */
    protected function _prepareSoldTogetherData()
    {
        $productIds = $this->getProductIds();

        if (!$this->getConfig('enabled') || !$productIds) {
            return $this;
        }

        $this->_collection = $this->prepareCollection($this->collectionFactory->create());
        $this->_collection->getSelect()
            ->joinInner(
                ['soldtogether' => $this->resource->getMainTable()],
                'soldtogether.related_id = e.entity_id',
                []
            )
            ->where('soldtogether.product_id IN (?)', $productIds)
            ->order('soldtogether.weight DESC');

        if ($this->_collection->count() === 0 && $this->getConfig('random')) {
            if ($collection = $this->getRandomCollection()) {
                $this->_collection = $collection;
            }
        }

        return $this;
    }

    /**
     * Before rendering html, but after trying to load cache
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->_prepareSoldTogetherData();
        return parent::_beforeToHtml();
    }

    /**
     * Prepare random collection of products from same category
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|false
     */
    protected function getRandomCollection()
    {
        $product = $this->getProduct();

        if (!$product) {
            return false;
        }

        if ($product->hasCategory()) {
            $category = $product->getCategory();
        } elseif ($product->hasCategoryIds()) {
            $categoryIds = $product->getCategoryIds();
            $category = $this->categoryFactory->create();
            $category->load(reset($categoryIds));
        } else {
            return false;
        }

        $collection = $this->prepareCollection($category->getProductCollection());
        $collection->getSelect()->order('rand()');

        return $collection;
    }

    /**
     * Apply common filters to the product collection
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function prepareCollection(
        \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
    ) {
        $collection->distinct(true)
            ->addAttributeToSelect('required_options')
            ->addStoreFilter()
            ->setVisibility(
                $this->catalogProductVisibility->getVisibleInCatalogIds()
            )
            ->addAttributeToFilter('entity_id', [
                'nin' => $this->getProductIds()
            ]);

        if ($this->moduleManager->isEnabled('Magento_Checkout')) {
            $this->_addProductAttributesAndPrices($collection);
        }

        if (!$this->getConfig('options')) {
            $collection->getSelect()
                ->where('e.type_id IN (?)', ['simple', 'virtual']);
        }

        if (!$this->getConfig('out')) {
            $this->stockHelper->addInStockFilterToCollection($collection);
        }

        $collection->getSelect()->limit($this->getProductsCount());

        return $collection;
    }

    /**
     * Get price format
     *
     * @return array
     */
    public function getPriceFormat()
    {
        return $this->localeFormat->getPriceFormat();
    }

    /**
     * Get collection items
     *
     * @return \Magento\Framework\Data\Collection\AbstractDb
     */
    public function getItems()
    {
        return $this->_collection;
    }

    /**
     * Get tax display config
     *
     * @return string
     */
    public function getTaxDisplayConfig()
    {
        return $this->_scopeConfig->getValue(
            "tax/display/type",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get IDs of products
     *
     * @return array
     */
    public function getIdentities()
    {
        $identities = [];
        if ($this->getItems()) {
            foreach ($this->getItems() as $item) {
                $identities = array_merge($identities, $item->getIdentities());
            }
        }

        return $identities;
    }

    /**
     * Get config value
     *
     * @param  string $key
     * @return string
     */
    public function getConfig($key)
    {
        return $this->_scopeConfig->getValue(
            sprintf("soldtogether/%s/%s", static::SOLDTOGETHER_ENTITY, $key),
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return integer
     */
    public function getProductsCount()
    {
        if (!$this->hasData('products_count')) {
            $this->setData('products_count', $this->getConfig('count'));
        }
        return (int) $this->getData('products_count');
    }

    /**
     * Get layout style for block
     *
     * @return string
     */
    public function getLayoutStyle()
    {
        if (!$this->hasData('layout_style')) {
            $this->setData('layout_style', $this->getConfig('layout'));
        }

        return $this->getData('layout_style');
    }

    /**
     * Return HTML block with price for current product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getCurrentProductPrice()
    {
        if ($price = $this->getLayout()->getBlock('product.price.render.bundle.customization')) {
            // there is price renderer for bundle product price - use it
            return $price->toHtml();
        }

        return $this->getProductPrice($this->getProduct());
    }

    /**
     * Get init parameters for slick carousel
     *
     * @param  string  $format
     * @param  boolean $isRtl
     * @return string|array
     */
    public function getSlickCarouselParams(
        $format = 'json',
        $isRtl = false
    ) {
        $params = [
            'slidesToShow' => 5,
            'slidesToScroll' => 5,
            'dots' => false,
            'rtl' => $isRtl,
            'rows' => 0,
            'autoplay' => false,
            'responsive' => [
                [
                    'breakpoint' => 1024,
                    'settings' => [
                        'slidesToShow' => 4,
                        'slidesToScroll' => 4
                    ]
                ],
                [
                    'breakpoint' => 600,
                    'settings' => [
                        'slidesToShow' => 3,
                        'slidesToScroll' => 3
                    ]
                ],
                [
                    'breakpoint' => 480,
                    'settings' => [
                        'slidesToShow' => 2,
                        'slidesToScroll' => 2
                    ]
                ],
                [
                    'breakpoint' => 376,
                    'settings' => [
                        'slidesToShow' => 1,
                        'slidesToScroll' => 1
                    ]
                ]
            ]
        ];

        if ($format == 'json') {
            return json_encode($params, JSON_HEX_APOS);
        }

        return $params;
    }
}

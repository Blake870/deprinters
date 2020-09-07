<?php

namespace Swissup\Highlight\Block\ProductList;

class All extends \Magento\Catalog\Block\Product\ListProduct implements \Magento\Widget\Block\BlockInterface
{
    const PAGE_TYPE = null;

    /**
     * Default toolbar block name
     *
     * @var string
     */
    protected $_defaultToolbarBlock = 'Swissup\Highlight\Block\ProductList\Toolbar';

    /**
     * @var \Magento\Catalog\Block\Product\Widget\Html\Pager
     */
    protected $widgetPager;

    protected $widgetPageVarName = 'hap';

    /**
     * @var \Swissup\Highlight\Block\ProductList\Toolbar
     */
    protected $toolbar;

    protected $widgetPriceSuffix = 'all';

    protected $widgetCssClass = 'highlight-all';

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * Catalog product visibility
     *
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $catalogProductVisibility;

    /**
     * @var \Swissup\Highlight\Helper\Page
     */
    protected $pageHelper;

    /**
     * @var \Swissup\Highlight\Helper\Conditions
     */
    protected $highlightConditions;

    /**
     * @var \Magento\Rule\Model\Condition\Sql\Builder
     */
    protected $sqlBuilder;

    /**
     * @var \Magento\CatalogWidget\Model\Rule
     */
    protected $rule;

    /**
     * @var \Magento\Widget\Helper\Conditions
     */
    protected $conditionsHelper;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param \Swissup\Highlight\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Swissup\Highlight\Helper\Page $highlightHelper
     * @param \Swissup\Highlight\Helper\Conditions $highlightConditions
     * @param \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder
     * @param \Magento\CatalogWidget\Model\RuleFactory $ruleFactory
     * @param \Magento\Widget\Helper\Conditions $conditionsHelper
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Swissup\Highlight\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Framework\App\Http\Context $httpContext,
        \Swissup\Highlight\Helper\Page $highlightHelper,
        \Swissup\Highlight\Helper\Conditions $highlightConditions,
        \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder,
        \Magento\CatalogWidget\Model\RuleFactory $ruleFactory,
        \Magento\Widget\Helper\Conditions $conditionsHelper,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->httpContext = $httpContext;
        $this->pageHelper = $highlightHelper;
        $this->highlightConditions = $highlightConditions;
        $this->sqlBuilder = $sqlBuilder;
        $this->rule = $ruleFactory->create();
        $this->conditionsHelper = $conditionsHelper;
        $this->priceCurrency = $priceCurrency;
        parent::__construct(
            $context,
            $postDataHelper,
            $layerResolver,
            $categoryRepository,
            $urlHelper,
            $data
        );
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function _getProductCollection()
    {
        \Magento\Framework\Profiler::start(__METHOD__);
        if ($this->_productCollection === null) {
            /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
            $collection = $this->productCollectionFactory->create($this->getProductCollectionType());

            $collection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());
            $collection = $this->_addProductAttributesAndPrices($collection)
                ->addStoreFilter()
                ->setPageSize($this->getPageSize())
                ->setCurPage($this->getCurrentPage());

            // Don't use _catalogLayer because it filters the products by current category
            // which doesn't works good on the homepage when `use_category_in_url` option
            // is used and products are not linked to the site's root category.
            // @see Magento\Catalog\Model\Layer\Category\CollectionFilter::filter
            //  `->addUrlRewrite($category->getId())`
            //
            // The main downside of this change - is product urls will not include
            // parent category. Just like Magento's widgets.
            //
            // We will revert this change and add note to the docs about adding products
            // to the root category in case of some serious degradation.
            //
            // $this->_catalogLayer->prepareProductCollection($collection);
            // $collection->addStoreFilter();

            $this->prepareProductCollection($collection);

            $this->_productCollection = $collection;
        }
        \Magento\Framework\Profiler::stop(__METHOD__);
        return $this->_productCollection;
    }

    public function getProductCollection()
    {
        return $this->_getProductCollection();
    }

    public function getProductCollectionType()
    {
        return \Swissup\Highlight\Model\ResourceModel\Product\CollectionFactory::TYPE_DEFAULT;
    }

    /**
     * Use this method to apply manual filters, etc
     *
     * @param  \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return void
     */
    public function prepareProductCollection($collection)
    {
        \Magento\Framework\Profiler::start(__METHOD__);
        $conditions = $this->getConditions();
        $conditions->collectValidatedAttributes($collection);
        $this->sqlBuilder->attachConditionToCollection($collection, $conditions);
        \Magento\Framework\Profiler::stop(__METHOD__);
    }

    /**
     * @return \Magento\Rule\Model\Condition\Combine
     */
    protected function getConditions()
    {
        $conditions = $this->getConditionsDecoded();

        foreach ($conditions as $key => $condition) {
            if (!empty($condition['attribute'])
                && in_array($condition['attribute'], ['special_from_date', 'special_to_date'])
            ) {
                $conditions[$key]['value'] = date('Y-m-d H:i:s', strtotime($condition['value']));
            }

            $conditions[$key] = $this->prepareCondition($condition);
        }

        $this->rule->loadPost(['conditions' => $conditions]);
        return $this->rule->getConditions();
    }

    /**
     * Modify condition item if needed
     *
     * @param  array $condition
     * @return array
     */
    private function prepareCondition($condition)
    {
        if (empty($condition['attribute'])) {
            return $condition;
        }

        // 'current' category filter
        if ($condition['attribute'] === 'category_ids'
            && strstr($condition['value'], 'current')) {

            $condition['value'] = str_replace(
                ['current', 'current+'],
                $this->getCategoryIds(),
                $condition['value']
            );

            $operator = $condition['operator'];
            switch ($condition['operator']) {
                case '==':
                    $operator = '()';
                    break;
                case '!=':
                    $operator = '!()';
                    break;
            }
            $condition['operator'] = $operator;
        }

        return $condition;
    }

    /**
     * Returns currently viewed, comma separated category ids, if
     * 'current' condition is used. Otherwise returns empty string.
     *
     * @return string
     */
    public function getCategoryIds()
    {
        $ids = $this->getData('category_ids');
        if ($ids !== null) {
            return $ids;
        }

        $categoryIds = [];
        foreach ($this->getConditionsDecoded() as $condition) {
            if (empty($condition['attribute'])
                || $condition['attribute'] !== 'category_ids'
                || !strstr($condition['value'], 'current')) {

                continue;
            }

            $withChildren = (bool) strstr($condition['value'], 'current+');
            $categoryIds = $this->highlightConditions->getCurrentCategoryIds(
                $withChildren
            );

            if ($withChildren) {
                break;
            }
        }

        $this->setData('category_ids', implode(',', $categoryIds));

        return $this->getData('category_ids');
    }

    /**
     * @return array
     */
    public function getConditionsDecoded()
    {
        if ($this->hasData('conditions_decoded')) {
            return $this->getData('conditions_decoded');
        }

        $conditions = $this->getData('conditions_encoded')
            ? $this->getData('conditions_encoded')
            : $this->getData('conditions');

        if ($conditions) {
            $conditions = $this->conditionsHelper->decode($conditions);
        }

        if (!$conditions) {
            $conditions = [];
        }

        $this->setData('conditions_decoded', $conditions);

        return $conditions;
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\Magento\Catalog\Model\Product::CACHE_TAG];
    }


    /**********************************************************
    ******************** Widget Specific Methods **************
    **********************************************************/
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
        if (false === $this->getIsWidget()) {
            return parent::getCacheKeyInfo();
        }

        $conditions = $this->getData('conditions')
            ? $this->getData('conditions')
            : $this->getData('conditions_encoded');

        return [
            'HIGHLIGHT',
            $this->priceCurrency->getCurrency()->getCode(),
            $this->_storeManager->getStore()->getId(),
            $this->_design->getDesignTheme()->getId(),
            $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP),
            'template' => $this->getTemplate(),
            $this->getAttributeCode(),
            $this->getCarousel(),
            $this->getMode(),
            $this->getOrder(),
            $this->getDir(),
            $this->getCurrentPage(),
            $this->getProductsCount(),
            $this->getLimit(),
            $this->showPager(),
            $this->getPriceSuffix(),
            $this->getCssClass(),
            $this->getPageVarName(),
            $conditions,
            $this->getCategoryIds(),
        ];
    }

    public function getTemplate()
    {
        if ($this->getHideWhenFilterIsUsed()) {
            if ($this->_request->getParam('p', 1) > 1) {
                return '';
            }

            if (count($this->_catalogLayer->getState()->getFilters())) {
                return '';
            }
        }

        if (empty($this->_template)) {
            $this->_template = $this->getCustomTemplate();
        } else {
            // deprecated templates
            $deprecatedTemplates = [
                'product/widget/content/grid.phtml',
                'Swissup_Highlight::product/widget/content/grid.phtml',
                'product/widget/content/list.phtml',
                'Swissup_Highlight::product/widget/content/list.phtml',
                'product/widget/content/grid-carousel.phtml',
                'Swissup_Highlight::product/widget/content/grid-carousel.phtml',
            ];
            if (in_array($this->_template, $deprecatedTemplates)) {
                $this->setData('mode', 'grid');

                if (strpos($this->_template, 'list.phtml') !== false) {
                    $this->setData('mode', 'list');
                } elseif (strpos($this->_template, 'grid-carousel.phtml') !== false) {
                    $this->setData('carousel', true);
                }

                $this->_template = 'Swissup_Highlight::product/list.phtml';
            }
        }

        return $this->_template;
    }

    /**
     * Used in Magento's < 2.2.4
     * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    public function getToolbarBlock()
    {
        if ($this->toolbar) {
            return $this->toolbar;
        }

        $toolbar = parent::getToolbarBlock();
        $this->initToolbar($toolbar);
        $this->toolbar = $toolbar;

        return $toolbar;
    }

    /**
     * Magento >= 2.2.4 compatibility
     * @return void
     */
    protected function _beforeToHtml()
    {
        if (method_exists($this, 'getToolbarFromLayout')
            && !$this->getToolbarFromLayout()) {

            $this->setToolbarBlockName($this->getToolbarBlock()->getNameInLayout());
        }

        return parent::_beforeToHtml();
    }

    /**
     * Use this method to apply manual sort order, etc
     *
     * @param  \Magento\Catalog\Block\Product\ProductList\Toolbar $toolbar
     * @return void
     */
    protected function initToolbar($toolbar)
    {
        $orders = array_keys($toolbar->getAvailableOrders());
        $defaultOrder = $this->getDefaultSortField();
        if (!in_array($defaultOrder, $orders)) {
            $toolbar->addOrderToAvailableOrders($this->getDefaultSortField(), $this->getDefaultSortFieldLabel());
            $toolbar->setDefaultOrder($this->getDefaultSortField());
            $toolbar->setDefaultDirection($this->getDefaultSortDirection());
        }

        if (false !== $this->getIsWidget()) {
            // $toolbar->setData('_current_grid_mode', $this->getMode());
            $toolbar->setData('_current_limit', $this->getProductsCount());
            $toolbar->setData('_current_page', $this->getCurrentPage());
            $toolbar->setData('_current_grid_direction', $this->getDir());

            // additional sort order parameter, use it to sort by attribute
            if ($this->hasOrder() && $this->getOrder() !== 'default') {
                $order = $this->getOrder();
                $toolbar->setSkipOrder(true);
                if (in_array(strtolower($order), ['rand()', 'rand', 'random'])) {
                    $this->getProductCollection()->getSelect()->order(new \Zend_Db_Expr('RAND()'));
                } else {
                    $this->getProductCollection()->setOrder($order, $this->getDir());
                }
            }
        }

        // sort by column, alias, etc
        if ($this->getRawOrder()) {
            $toolbar->setSkipOrder(true);
            $this->getProductCollection()->getSelect()->order($this->getRawOrder());
        }
    }

    public function getDefaultSortField()
    {
        return 'position';
    }

    public function getDefaultSortDirection()
    {
        return 'ASC';
    }

    public function getDir()
    {
        if (!$this->hasData('dir')) {
            return $this->getDefaultSortDirection();
        }
        return $this->getData('dir');
    }

    /**
     * Get number of current page based on query value
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return abs((int)$this->getRequest()->getParam($this->getPageVarName()));
    }

    /**
     * Retrieve how many products should be displayed
     *
     * @return int
     */
    public function getProductsCount()
    {
        if (!$this->hasData('products_count')) {
            return 5;
        }
        return $this->getData('products_count');
    }

    /**
     * Retrieve how many products should be displayed in pagination
     *
     * @return int
     */
    protected function getLimit()
    {
        $pageCount = $this->getPageCount();
        if (!$pageCount) {
            $pageCount = 1;
        }
        return $pageCount * $this->getProductsCount();
    }

    /**
     * Return flag whether pager need to be shown or not
     *
     * @return bool
     */
    public function showPager()
    {
        return $this->getPageCount() > 1;
    }

    public function getPriceSuffix()
    {
        if (!$this->hasData('price_suffix')) {
            $this->setData('price_suffix', $this->widgetPriceSuffix);
        }
        return $this->getData('price_suffix');
    }

    public function getCssClass()
    {
        $cssClasses = [
            'block-highlight',
            $this->getCarousel() ? 'highlight-carousel' : '',
            $this->getMode() ? 'highlight-' . $this->getMode() : '',
            $this->getMode() === 'grid' ? 'highlight-cols-' . $this->getColumnCount() : '',
            $this->widgetCssClass,
            $this->getData('css_class'),
        ];

        return implode(' ', array_filter($cssClasses));
    }

    public function getPageVarName()
    {
        if (!$this->hasData('page_var_name')) {
            $this->setData('page_var_name', $this->widgetPageVarName);
        }
        return $this->getData('page_var_name');
    }

    /**
     * Retrieve current view mode
     *
     * @return string
     */
    public function getMode()
    {
        if (false === $this->getIsWidget()) {
            return parent::getMode();
        }
        return $this->getData('mode') ? $this->getData('mode') : 'grid';
    }

    public function getPageUrl()
    {
        if ($this->hasData('page_url')) {
            return $this->pageHelper->getDirectUrl($this->getData('page_url'));
        }

        if (!static::PAGE_TYPE) {
            return false;
        }
        return $this->pageHelper->getPageUrl(static::PAGE_TYPE);
    }

    /**
     * Render pagination HTML
     *
     * @return string
     */
    public function getPagerHtml()
    {
        if ($this->showPager() && $this->getPagerBlock()) {
            return $this->getPagerBlock()->toHtml();
        }
        return '';
    }

    /**
     * @return \Magento\Catalog\Block\Product\Widget\Html\Pager
     */
    public function getPagerBlock()
    {
        if (!$this->widgetPager) {
            $this->widgetPager = $this->getLayout()->createBlock(
                'Magento\Catalog\Block\Product\Widget\Html\Pager',
                $this->getToolbarBlock()->getNameInLayout() . '_pager'
            );

            $this->widgetPager->setUseContainer(true)
                ->setShowAmounts(true)
                ->setShowPerPage(false)
                ->setPageVarName($this->getPageVarName())
                ->setLimit($this->getProductsCount())
                ->setTotalLimit($this->getLimit())
                ->setCollection($this->getProductCollection());
        }
        return $this->widgetPager;
    }

    public function getToolbarHtml()
    {
        if (false === $this->getIsWidget()) {
            return parent::getToolbarHtml();
        }
        return '';
    }

    /**
     * Render block HTML and wrap it into highlight markup, if needed
     *
     * @return string
     */
    protected function _toHtml()
    {
        // return '';
        if (!$this->getTemplate()) {
            return '';
        }

        $html = parent::_toHtml();
        if (!$html
            || !$this->getProductCollection()
            || !$this->getProductCollection()->getSize()
        ) {
            return '';
        }

        if ($this->getDisableWrapper()) {
            return $html;
        }

        \Magento\Framework\Profiler::start(__METHOD__ . 'toHtml');
        $block = $this->getLayout()
            ->createBlock(\Magento\Framework\View\Element\Template::class)
            ->setTemplate('Swissup_Highlight::block.phtml')
            ->setHighlightBlock($this)
            ->setContent($html);

        $return = $block->toHtml();
        \Magento\Framework\Profiler::stop(__METHOD__. 'toHtml');

        return $return;
    }

    /**
     * Return HTML block with price
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $priceType
     * @param string $renderZone
     * @param array $arguments
     * @return string
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getProductPriceHtml(
        \Magento\Catalog\Model\Product $product,
        $priceType = null,
        $renderZone = \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
        array $arguments = []
    ) {
        if (!isset($arguments['zone'])) {
            $arguments['zone'] = $renderZone;
        }
        $arguments['zone'] = isset($arguments['zone'])
            ? $arguments['zone']
            : $renderZone;
        $arguments['price_id'] = isset($arguments['price_id'])
            ? $arguments['price_id']
            : 'old-price-' . $product->getId() . '-' . $priceType;
        $arguments['include_container'] = isset($arguments['include_container'])
            ? $arguments['include_container']
            : true;
        $arguments['display_minimal_price'] = isset($arguments['display_minimal_price'])
            ? $arguments['display_minimal_price']
            : true;

        /** @var \Magento\Framework\Pricing\Render $priceRender */
        $priceRender = $this->getPriceRender();

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                $product,
                $arguments
            );
        }
        return $price;
    }

    /**
     * @return Render
     */
    protected function getPriceRender()
    {
        $block = $this->getLayout()->getBlock('product.price.render.default');
        if ($block) {
            $block->setData('is_product_list', true);
        }
        return $block;
    }

    public function getTitleImageUrl()
    {
        // get title image from block data
        if ($imageUrl = $this->getData('title_image_url')) {
            if (false !== strpos($imageUrl, '://')) {
                return $imageUrl;
            }

            return $this->_storeManager
                ->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
                . $imageUrl;
        }

        return '';
    }
}

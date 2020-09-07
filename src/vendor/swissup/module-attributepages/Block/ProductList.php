<?php
namespace Swissup\Attributepages\Block;

use Magento\Catalog\Block\Product\ListProduct;
use Magento\Widget\Block\BlockInterface;

class ProductList extends ListProduct implements BlockInterface
{
    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;
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
     * Page view helper
     *
     * @var \Swissup\Attributepages\Helper\Page\View
     */
    protected $pageViewHelper;
    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder
     * @param \Magento\CatalogWidget\Model\Rule $rule
     * @param \Magento\Widget\Helper\Conditions $conditionsHelper
     * @param \Swissup\Attributepages\Helper\Page\View $pageViewHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder,
        \Magento\CatalogWidget\Model\Rule $rule,
        \Magento\Widget\Helper\Conditions $conditionsHelper,
        \Swissup\Attributepages\Helper\Page\View $pageViewHelper,
        array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->sqlBuilder = $sqlBuilder;
        $this->rule = $rule;
        $this->conditionsHelper = $conditionsHelper;
        $this->pageViewHelper = $pageViewHelper;
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
        if ($this->_productCollection === null) {
            /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
            $collection = $this->productCollectionFactory->create();
            $this->_catalogLayer->prepareProductCollection($collection);
            $collection->addStoreFilter();
            $this->prepareProductCollection($collection);
            $this->_productCollection = $collection;
        }
        return $this->_productCollection;
    }

    public function getProductCollection()
    {
        return $this->_getProductCollection();
    }
    /**
     * Use this method to apply manual filters, etc
     *
     * @param  \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return void
     */
    public function prepareProductCollection($collection)
    {
        $conditions = $this->getConditions();
        $conditions->collectValidatedAttributes($collection);
        $this->sqlBuilder->attachConditionToCollection($collection, $conditions);
        try {
            $currentPage = $this->pageViewHelper
                ->getRegistryObject('attributepages_current_page');
            $attributeCode = $currentPage->getAttribute()->getAttributeCode();
            $optionId = $currentPage->getOptionId();
            $collection->addAttributeToFilter($attributeCode, ['finset' => $optionId]);

            // do not show products that are not assigned to any visible category
            // fixes magento bug, when product always linked to root category in index table
            $connection = $collection->getSelect()->getConnection();
            $rootCategoryId = $this->_storeManager->getStore()->getRootCategoryId();
            $collection->getSelect()
                ->distinct()
                ->join(
                    ['sap_catalog_category_product' => $collection->getTable('catalog_category_product')],
                    'e.entity_id=sap_catalog_category_product.product_id',
                    []
                )
                ->join(
                    ['sap_catalog_category_entity' => $collection->getTable('catalog_category_entity')],
                    'sap_catalog_category_product.category_id=sap_catalog_category_entity.entity_id',
                    []
                )
                ->where(
                    $connection->quoteInto('sap_catalog_category_entity.path = ?', "1/{$rootCategoryId}")
                    . ' OR ' .
                    $connection->quoteInto('sap_catalog_category_entity.path LIKE ?', "1/{$rootCategoryId}/%")
                );
        } catch (\Exception $e) {
            $this->setTemplate(null);
            $this->setCustomTemplate(null);
        }
    }
    /**
     * @return \Magento\Rule\Model\Condition\Combine
     */
    protected function getConditions()
    {
        $conditions = $this->getData('conditions_encoded')
            ? $this->getData('conditions_encoded')
            : $this->getData('conditions');
        if ($conditions) {
            $conditions = $this->conditionsHelper->decode($conditions);
        }
        $this->rule->loadPost(['conditions' => $conditions]);
        return $this->rule->getConditions();
    }
}

<?php
namespace Swissup\Attributepages\Block\Attribute;

use Swissup\Attributepages\Model\Entity as AttributepagesModel;
/**
 * Class attribute based pages list
 * @package Swissup\Attributepages\Block\Attribute
 */
class PagesList extends \Magento\Framework\View\Element\Template
    implements \Magento\Framework\DataObject\IdentityInterface
{
    /**
     * @var Swissup\Attributepages\Model\ResourceModel\Entity\Collection
     */
    protected $attributeCollection;
    /**
     * @var Swissup\Attributepages\Model\ResourceModel\Entity\CollectionFactory
     */
    protected $attributeCollectionFactory;
    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Swissup\Attributepages\Model\ResourceModel\Entity\CollectionFactory $attributeCollectionFactory,
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Swissup\Attributepages\Model\ResourceModel\Entity\CollectionFactory $attributeCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->attributeCollectionFactory = $attributeCollectionFactory;
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
                'cache_lifetime' => 86400
            ]
        );
    }
    /**
     * Retrieve loaded category collection
     *
     * @return Swissup\Attributepages\Model\ResourceModel\Entity\Collection
     */
    protected function _getAttributeCollection()
    {
        if (null === $this->attributeCollection) {
            $storeId = $this->_storeManager->getStore()->getId();
            $this->attributeCollection = $this->attributeCollectionFactory->create()
                ->addAttributeOnlyFilter()
                ->addUseForAttributePageFilter()
                ->addStoreFilter($storeId)
                ->setOrder('main_table.title', 'asc');
            if ($excludedPages = $this->getExcludedPages()) {
                $excludedPages = explode(',', $excludedPages);
                $this->attributeCollection
                    ->addFieldToFilter('identifier', ['nin' => $excludedPages]);
            }
            if ($includedPages = $this->getIncludedPages()) {
                $includedPages = explode(',', $includedPages);
                $this->attributeCollection
                    ->addFieldToFilter('identifier', ['in' => $includedPages]);
            }
            // filter pages with the same urls: linked to All Store Views and current store
            $urls = $this->attributeCollection->getColumnValues('identifier');
            $duplicateUrls = [];
            foreach (array_count_values($urls) as $url => $count) {
                if ($count > 1) {
                    $duplicateUrls[] = $url;
                }
            }
            foreach ($duplicateUrls as $url) {
                $idsToRemove = [];
                $removeFlag = false;
                $attributes = $this->attributeCollection->getItemsByColumnValue('identifier', $url);
                foreach ($attributes as $attribute) {
                    if ($attribute->getStoreId() !== $storeId) {
                        $idsToRemove[] = $attribute->getId();
                    } else {
                        $removeFlag = true;
                    }
                }
                if ($removeFlag) {
                    foreach ($idsToRemove as $id) {
                        $this->attributeCollection->removeItemByKey($id);
                    }
                }
            }
        }
        return $this->attributeCollection;
    }
    /**
     * Retrieve loaded category collection
     *
     * @return Swissup\Attributepages\Model\ResourceModel\Entity\Collection
     */
    public function getLoadedAttributeCollection()
    {
        return $this->_getAttributeCollection();
    }
    public function setCollection($collection)
    {
        $this->attributeCollection = $collection;
        return $this;
    }
    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [AttributepagesModel::CACHE_TAG . '_' . 'pages_list'];
    }
    /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return [
            'ATTRIBUTEPAGES',
            $this->_storeManager->getStore()->getId(),
            $this->_design->getDesignTheme()->getId(),
            $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP),
            'template' => $this->getTemplate(),
            $this->getColumnCount(),
            $this->getExcludedPages(),
            $this->getIncludedPages()
        ];
    }
}

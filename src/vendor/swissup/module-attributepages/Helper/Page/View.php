<?php
namespace Swissup\Attributepages\Helper\Page;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Swissup\Attributepages\Model\Entity as AttributepagesModel;

class View extends AbstractHelper
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * Attribute pages collection factory
     * @var \Swissup\Attributepages\Model\ResourceModel\Entity\CollectionFactory
     */
    protected $attrpagesCollectionFactory;
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Swissup\Attributepages\Model\ResourceModel\Entity\CollectionFactory $attrpagesCollectionFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Swissup\Attributepages\Model\ResourceModel\Entity\CollectionFactory $attrpagesCollectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory
    )
    {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->attrpagesCollectionFactory = $attrpagesCollectionFactory;
        $this->coreRegistry = $coreRegistry;
        $this->categoryFactory = $categoryFactory;
    }
    /**
     * Init current and parent pages in \Magento\Framework\Registry
     *
     * @param  mixed $pageId       Current page identifier or id
     * @param  mixed $parentPageId Parent page identifier or id
     * @param  string $field       identifier|entity_id
     * @return AttributepagesModel|false Current page model
     */
    public function initPagesInRegistry($pageId, $parentPageId = null, $field = 'identifier')
    {
        if (!$pageId) {
            return false;
        }
        $storeId = $this->storeManager->getStore()->getId();
        $pageIds = [$pageId, htmlentities($pageId), $parentPageId, htmlentities($parentPageId)];
        $collection = $this->attrpagesCollectionFactory->create()
            ->addFieldToFilter(
                $field,
                [
                    'in' => array_filter($pageIds)
                ]
            )
            ->addStoreFilter($storeId);
        // fix for the same identifiers for different options/pages
        $uniquePages = [];
        foreach ($collection as $page) {
            if (!$page->getUseForAttributePage()) {
                continue;
            }
            if (!empty($uniquePages[$page->getIdentifier()])) {
                if ($page->getStoreId() !== $storeId) {
                    continue;
                }
            }
            $uniquePages[$page->getIdentifier()] = $page;
        }
        $size = count($uniquePages);
        if (!$size) {
            return false;
        }
        $index = $size - 1;
        foreach ($uniquePages as $page) {
            // fix for the case insensitive mysql query
            if (!in_array($page->getData($field), $pageIds)) {
                continue;
            }

            if ($page->getData($field) == $pageId || $page->getData($field) == htmlentities($pageId)) {
                $key = 'attributepages_current_page';
            } else {
                $key = 'attributepages_parent_page';
            }
            $this->coreRegistry->register($key, $page);
        }
        if (!$page = $this->coreRegistry->registry('attributepages_current_page')) {
            return false;
        }
        if ($parent = $this->coreRegistry->registry('attributepages_parent_page')) {
            // disallow links like brands/color or black/white or black/htc
            if ($parent->isOptionBasedPage() || $page->isAttributeBasedPage()) {
                return false;
            }
            // disallow links like color/htc or brands/white
            if ($parent->getAttributeId() !== $page->getAttributeId()) {
                return false;
            }
        }
        // disallow direct link to option page: example.com/htc
        $key = 'attributepages/seo/allow_direct_option_link';
        $allowDirectOptionLink = $this->scopeConfig->getValue($key, ScopeInterface::SCOPE_STORE);
        if ($page->isOptionBasedPage()
            && !$parent
            && !$allowDirectOptionLink) {
            return false;
        }
        // root category is always registered as current_category
        $categoryId = $this->storeManager->getStore()->getRootCategoryId();
        if ($categoryId && !$this->coreRegistry->registry('current_category')) {
            $category = $this->categoryFactory->create()
                ->setStoreId($storeId)
                ->load($categoryId);
            $this->coreRegistry->register('current_category', $category);
        }
        return $page;
    }
    /**
     * Get object saved in registry
     * @param  String $id
     * @return mixed
     */
    public function getRegistryObject($id)
    {
        return $this->coreRegistry->registry($id);
    }
}

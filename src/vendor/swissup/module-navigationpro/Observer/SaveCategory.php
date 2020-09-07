<?php

namespace Swissup\Navigationpro\Observer;

use Magento\Store\Model\Store;
use Magento\Framework\Event\ObserverInterface;
use Swissup\Navigationpro\Model\Item;
use Swissup\Navigationpro\Model\Menu;

class SaveCategory implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\CacheInterface
     */
    private $cache;

    /**
     * @var \Swissup\Navigationpro\Model\ItemFactory
     */
    private $itemFactory;

    /**
     * @var \Swissup\Navigationpro\Model\ResourceModel\Item\CollectionFactory
     */
    private $itemCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\CategoryRepository
     */
    private $categoryRepository;

    /**
     * @param \Magento\Framework\App\CacheInterface $cache
     * @param \Swissup\Navigationpro\Model\ItemFactory $itemFactory
     * @param \Swissup\Navigationpro\Model\ResourceModel\Item\CollectionFactory $itemCollectionFactory
     * @param \Magento\Catalog\Model\CategoryRepository $categoryRepository
     */
    public function __construct(
        \Magento\Framework\App\CacheInterface $cache,
        \Swissup\Navigationpro\Model\ItemFactory $itemFactory,
        \Swissup\Navigationpro\Model\ResourceModel\Item\CollectionFactory $itemCollectionFactory,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository
    ) {
        $this->cache = $cache;
        $this->itemFactory = $itemFactory;
        $this->itemCollectionFactory = $itemCollectionFactory;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Add new category items to existing menu's
     *
     * @param  \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $category = $observer->getEvent()->getCategory();

        /** @var \Swissup\Navigationpro\Model\ResourceModel\Item\Collection $items */
        $items = $this->itemCollectionFactory->create()
            ->addFieldToFilter('remote_entity_type', Item::REMOTE_ENTITY_TYPE_CATEGORY);

        if ($category->getLevel() > 2) {
            $items->addFieldToFilter('remote_entity_id', $category->getParentId());

            if ($category->isObjectNew()) {
                $this->createNestedItems($items, $category);
            } else {
                $this->updateMenuCache($items->getColumnValues('menu_id'));
            }
        } else {
            $siblings = $this->categoryRepository
                ->get($category->getParentId())
                ->getChildren();

            if (!$siblings) {
                return;
            }

            $siblings = explode(',', $siblings);

            $items->addFieldToFilter('level', $category->getLevel())
                ->addFieldToFilter('remote_entity_id', $siblings)
                ->getSelect()
                ->group('menu_id');

            if ($category->isObjectNew()) {
                $this->createRootItems($items->getColumnValues('menu_id'), $category);
            } else {
                $this->updateMenuCache($items->getColumnValues('menu_id'));
            }
        }
    }

    /**
     * @param \Swissup\Navigationpro\Model\ResourceModel\Item\Collection $parentItems
     * @param \Magento\Catalog\Model\Category $remoteEntity
     * @return void
     */
    protected function createNestedItems($parentItems, $remoteEntity)
    {
        foreach ($parentItems as $parentItem) {
            $this->getItem($remoteEntity, $parentItem->getMenuId(), $parentItem)->save();
        }
    }

    /**
     * @param array $menuIds
     * @param \Magento\Catalog\Model\Category $remoteEntity
     * @return void
     */
    protected function createRootItems($menuIds, $remoteEntity)
    {
        foreach ($menuIds as $menuId) {
            $this->getItem($remoteEntity, $menuId)->save();
        }
    }

    /**
     * @param \Magento\Catalog\Model\Category $remoteEntity
     * @param integer $menuId
     * @param \Swissup\Navigationpro\Model\Item|null $parentItem
     * @return \Swissup\Navigationpro\Model\Item
     */
    protected function getItem($remoteEntity, $menuId, $parentItem = null)
    {
        $newItem = $this->itemFactory->create();
        $newItem->addData([
            'store_id' => Store::DEFAULT_STORE_ID,
            'menu_id'  => $menuId,
            'parent_id' => $parentItem ? $parentItem->getId() : null,
            'remote_entity_type' => Item::REMOTE_ENTITY_TYPE_CATEGORY,
            'remote_entity_id' => $remoteEntity->getId(),
            'position' => $remoteEntity->getPosition(),
        ]);

        if ($parentItem) {
            $newItem->setParentItem($parentItem);
        }

        return $newItem;
    }

    protected function updateMenuCache($menuIds)
    {
        $tags = [];

        foreach ($menuIds as $menuId) {
            $tags[] = Menu::CACHE_TAG . '_' . $menuId;
        }

        $this->cache->clean($tags);
    }
}

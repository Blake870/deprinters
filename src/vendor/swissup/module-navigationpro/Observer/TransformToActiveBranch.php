<?php

namespace Swissup\Navigationpro\Observer;

use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;

class TransformToActiveBranch implements ObserverInterface
{
    /**
     * @var Resolver
     */
    private $layerResolver;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Initialize dependencies.
     *
     * @param StoreManagerInterface $storeManager
     * @param Resolver $layerResolver
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Resolver $layerResolver
    ) {
        $this->storeManager = $storeManager;
        $this->layerResolver = $layerResolver;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $block = $observer->getBlock();
        if (!$block->getShowActiveBranch()) {
            return;
        }

        if ($block->getCategoryId()) {
            $categoryId = $block->getCategoryId();
        } else {
            if (!$currentCategory = $this->getCurrentCategory()) {
                return;
            }
            $categoryId = $currentCategory->getId();
        }

        $collection = $observer->getCollection();
        $storeId = $this->storeManager->getStore()->getId();

        /** @var \Swissup\Navigationpro\Model\Item $currentItem */
        $currentItem = $block->getMenu()->getItems($storeId)
            ->canAddRemoteEntities(false)
            ->addFilterByCategory($categoryId)
            ->setPageSize(1)
            ->getFirstItem();

        $filterMap = ['path'];
        $filterValues = [
            ['like' => $currentItem->getPath() . '/%']
        ];
        if ($block->getShowParent() === null || $block->getShowParent()) {
            $filterMap['parent_item'] = 'item_id';
            $filterMap['current_item'] = 'item_id';
            $filterValues['parent_item'] = $currentItem->getParentId();
            $filterValues['current_item'] = $currentItem->getId();
        }

        // get all children
        $collection
            ->addFieldToFilter('level', ['lteq' => $currentItem->getLevel() + 1])
            ->addFieldToFilter($filterMap, $filterValues);

        // change parent_id value to render all items together
        $parentId = $collection->getFirstItem()->getParentId();
        foreach ($collection as $item) {
            $item->setParentId($parentId);
        }

        // update css classes
        $parentItem = $collection->getItemById($currentItem->getParentId());
        if ($parentItem) {
            $parentItem->setCssClass($parentItem->getCssClass() . ' navpro-back');
        }
        $activeItem = $collection->getItemById($currentItem->getId());
        if ($activeItem) {
            $activeItem->setCssClass($activeItem->getCssClass() . ' navpro-current');
        }
    }

    /**
     * Get current Category from catalog layer
     *
     * @return \Magento\Catalog\Model\Category
     */
    protected function getCurrentCategory()
    {
        $catalogLayer = $this->layerResolver->get();

        if (!$catalogLayer) {
            return null;
        }

        return $catalogLayer->getCurrentCategory();
    }
}

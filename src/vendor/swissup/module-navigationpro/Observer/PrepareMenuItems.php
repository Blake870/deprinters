<?php

namespace Swissup\Navigationpro\Observer;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Swissup\Navigationpro\Model\Item;

class PrepareMenuItems implements ObserverInterface
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
     * System event manager
     *
     *
     * @var ManagerInterface
     */
    private $eventManager;

    /**
     * Initialize dependencies.
     *
     * @param StoreManagerInterface $storeManager
     * @param Resolver $layerResolver
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Resolver $layerResolver,
        ManagerInterface $eventManager
    ) {
        $this->storeManager = $storeManager;
        $this->layerResolver = $layerResolver;
        $this->eventManager = $eventManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Swissup\Navigationpro\Block\Menu $block */
        $block = $observer->getBlock();
        $storeId = $this->storeManager->getStore()->getId();

        /** @var \Swissup\Navigationpro\Model\ResourceModel\Item\Collection $collection */
        $collection = $block->getMenu()->getVisibleItems($storeId);
        $currentCategory = $this->getCurrentCategory();

        // Allow to modify collection for various customizations
        $this->eventManager->dispatch(
            'swissup_navigationpro_menu_prepare_collection_load_before',
            [
                'collection' => $collection,
                'block' => $block,
            ]
        );

        $rootId = 0;
        if ($parentId = $collection->getFirstItem()->getParentId()) {
            $rootId = $parentId;
        }
        $mapping = [$rootId => $observer->getMenu()];

        /** @var \Swissup\Navigationpro\Model\Item $item */
        foreach ($collection as $item) {
            $parentId = $item->getParentId();
            if (!$parentId) {
                $parentId = 0;
            }
            if (!isset($mapping[$parentId])) {
                continue;
            }

            if ($item->isCategoryItem()) {
                if (!$item->getRemoteEntity() ||
                    !$item->getRemoteEntity()->getIsActive()
                ) {
                    continue;
                }
            }

            /** @var Node $parentItemNode */
            $parentItemNode = $mapping[$parentId];

            $itemNode = new Node(
                $this->getItemAsArray($item, $currentCategory),
                'id',
                $parentItemNode->getTree(),
                $parentItemNode
            );
            $parentItemNode->addChild($itemNode);

            $mapping[$item->getId()] = $itemNode;
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

    /**
     * Convert item to array
     *
     * @param \Swissup\Navigationpro\Model\Item $item
     * @param \Magento\Catalog\Model\Category $currentCategory
     * @return array
     */
    protected function getItemAsArray($item, $currentCategory)
    {
        return [
            'id'   => 'item-node-' . $item->getId(),
            'name' => $item->getName(),
            'html' => $item->getHtml(),
            'url'  => $item->getUrl(),
            'css_class' => $item->getCssClass(),
            'dropdown_settings' => $item->getDropdownSettings(),
            'has_active' => in_array((string)$item->getRemoteEntityId(), explode('/', $currentCategory->getPath()), true),
            'is_active'  => $item->getRemoteEntityId() == $currentCategory->getId(),
            'is_category' => $item->isCategoryItem(),
            'remote_entity' => $item->getRemoteEntity()
        ];
    }
}

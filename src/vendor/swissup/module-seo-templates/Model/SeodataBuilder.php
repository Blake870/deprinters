<?php

namespace Swissup\SeoTemplates\Model;

use Magento\Store\Model\StoreManagerInterface;

class SeodataBuilder
{
    /**
     * @var ResourceModel\Seodata\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var array
     */
    private $cache = [];

    /**
     * @param ResourceModel\Seodata\CollectionFactory $collectionFactory
     * @param StoreManagerInterface                   $storeManager
     */
    public function __construct(
        ResourceModel\Seodata\CollectionFactory $collectionFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Create DataObject with metadata.
     *
     * @param  int    $entityId
     * @param  string $entityType
     * @return \Magento\Framework\DataObject
     */
    public function create($entityId, $entityType)
    {
        $collection = $this->collectionFactory
            ->create()
            ->addFilter('entity_type', $entityType)
            ->addFilter('entity_id', $entityId)
            ->addStoreFilter($this->storeManager->getStore()->getId())
            ->addOrder('store_id', 'ASC');
        $seodata = new \Magento\Framework\DataObject;
        foreach ($collection as $item) {
            $item->load($item->getId()); // force unserialize;
            $seodata->addData($item->getMetadata());
        }

        return $seodata;
    }

    /**
     * Get DataObject with metadata (with memoization).
     *
     * @param  int    $entityId
     * @param  string $entityType
     * @return \Magento\Framework\DataObject
     */
    public function get($entityId, $entityType)
    {
        $key = "{$entityType}:{$entityId}";
        if (!isset($this->cache[$key])) {
            $this->cache[$key] = $this->create($entityId, $entityType);
        }

        return $this->cache[$key];
    }
}

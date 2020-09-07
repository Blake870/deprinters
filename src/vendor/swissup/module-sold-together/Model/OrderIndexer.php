<?php

namespace Swissup\SoldTogether\Model;

use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Swissup\SoldTogether\Model\OrderFactory;

class OrderIndexer extends AbstractIndexer
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var OrderFactory
     */
    protected $modelFactory;

    /**
     * @param CollectionFactory $collectionFactory
     * @param OrderFactory      $modelFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        OrderFactory $modelFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->modelFactory = $modelFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function collectData($pageNumber, $pageSize)
    {
        $result = [];
        $collection = $this->collectionFactory->create();
        $collection->setPageSize($pageSize);
        if ($pageNumber > $collection->getLastPageNumber()) {
            return $result;
        }

        $collection->setCurPage($pageNumber);
        foreach ($collection as $order) {
            $storeId = $order->getStoreId();
            $visibleItems = $order->getAllVisibleItems();
            $orderProducts = [];
            if (count($visibleItems) > 1) {
                foreach ($visibleItems as $product) {
                    $orderProducts[$product->getProductId()] = $product->getName();
                }

                foreach ($orderProducts as $productId => $productName) {
                    foreach ($orderProducts as $relatedId => $relatedName) {
                        if ($productId == $relatedId) {
                            continue;
                        }
                        $result[] = [
                            'product_id'   => $productId,
                            'related_id'   => $relatedId,
                            'product_name' => $productName,
                            'related_name' => $relatedName,
                            'store_id'     => 0,
                            'weight'       => 0,
                            'is_admin'     => 0
                        ];
                    }
                }
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getItemsToProcessCount()
    {
        return $this->collectionFactory->create()->getTotalCount();
    }
}

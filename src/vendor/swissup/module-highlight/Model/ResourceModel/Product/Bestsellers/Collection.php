<?php

namespace Swissup\Highlight\Model\ResourceModel\Product\Bestsellers;

class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
    /**
     * Init Select
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->joinOrderedQty();
        return $this;
    }

    protected function joinOrderedQty()
    {
        $connection = $this->getConnection();
        $orderJoinCondition = [
            'order.entity_id = order_items.order_id',
            $connection->quoteInto("order.state <> ?", \Magento\Sales\Model\Order::STATE_CANCELED),
        ];

        $this->getSelect()->join(
            ['order_items' => $this->getTable('sales_order_item')],
            'order_items.product_id=e.entity_id',
            [
                'popularity' => 'COUNT(order_items.qty_ordered)',
                'order_items_name' => 'order_items.name'
            ]
        )->join(
            ['order' => $this->getTable('sales_order')],
            implode(' AND ', $orderJoinCondition),
            []
        );

        $this->getSelect()
            ->where('parent_item_id IS NULL')
            ->group('e.entity_id');

        return $this;
    }

    public function filterByPopularity($min, $max = null)
    {
        if ($min && $max) {
            $this->filterByPopularity($min)->filterByPopularity(null, $max);
        } elseif ($min) {
            $this->getSelect()->having('COUNT(order_items.qty_ordered) >= ?', $min);
        } elseif ($max) {
            $this->getSelect()->having('COUNT(order_items.qty_ordered) <= ?', $max);
        }
        return $this;
    }

    /**
     * Fixed size calculation, when group by is used
     *
     * @return int
     */
    public function getSize()
    {
        if ($this->_totalRecords === null) {
            $sql = $this->getSelectCountSql();
            $this->_totalRecords = count($this->getConnection()->fetchAll($sql, $this->_bindParams));
        }
        return intval($this->_totalRecords);
    }
}

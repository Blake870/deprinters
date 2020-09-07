<?php

namespace Swissup\Highlight\Model\ResourceModel\Product\Popular;

class Collection extends \Magento\Reports\Model\ResourceModel\Product\Index\Viewed\Collection
{
    /**
     * Init Select
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->joinViews();
        return $this;
    }

    protected function joinViews()
    {
        $this->joinTable(
            ['idx_table' => $this->_getTableName()],
            'product_id=entity_id',
            [
                'product_id'    => 'product_id',
                'item_store_id' => 'store_id',
                'added_at'      => 'idx_table.added_at',
                'popularity'    => 'COUNT(e.entity_id)'
            ]
        );

        // group views of different users
        $this->getSelect()->group('e.entity_id');

        return $this;
    }

    public function filterByPopularity($min, $max = false)
    {
        if ($min && $max) {
            $this->filterByPopularity($min)->filterByPopularity(null, $max);
        } elseif ($min) {
            $this->getSelect()->having('COUNT(e.entity_id) >= ?', $min);
        } elseif ($max) {
            $this->getSelect()->having('COUNT(e.entity_id) <= ?', $max);
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

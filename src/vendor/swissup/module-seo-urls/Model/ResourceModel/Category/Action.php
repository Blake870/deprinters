<?php

namespace Swissup\SeoUrls\Model\ResourceModel\Category;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Action extends AbstractDb
{
    /**
     * @var array
     */
    protected $memoized = [];

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('catalog_category_entity', 'entity_id');
    }

    /**
     * Update labels data fr category in DB
     *
     * @param  \Magento\Framework\DataObject $category
     * @param  array                         $newLabels
     * @return void
     */
    public function updateInUrlLabels(
        \Magento\Framework\DataObject $category,
        array $newLabels = []
    ) {

        $newLabels = $newLabels;
        $oldLabels = [];
        foreach ($this->getInUrlLabels($category) as $label) {
            $oldLabels[$label['store_id']] = $label['value'];
        }

        $newLabels += $oldLabels;

        $table = $this->getTable('swissup_seourls_category_label');
        $insert = array_diff($newLabels, $oldLabels);
        $delete = array_diff($oldLabels, $newLabels);
        if ($delete) {
            $where = array(
                'entity_id = ?' => (int) $category->getId(),
                'store_id IN (?)' => array_keys($delete)
            );
            $this->getConnection()->delete($table, $where);
        }

        $insert = array_filter($insert);
        if ($insert) {
            $data = [];
            foreach ($insert as $storeId => $value) {
                $data[] = [
                    'entity_id'  => (int) $category->getId(),
                    'store_id' => (int) $storeId,
                    'value' => $value
                ];
            }

            $this->getConnection()->insertMultiple($table, $data);
        }

        unset($this->memoized[$category->getId()]);
    }

    /**
     * Read in-url category labels from DB
     *
     * @param  \Magento\Framework\DataObject $cartegory
     * @return array
     */
    public function getInUrlLabels(\Magento\Framework\DataObject $category)
    {
        $categoryId = $category->getId();
        if (!isset($this->memoized[$categoryId])) {
            $connection = $this->getConnection();
            $select = $connection->select()
                ->from(
                    ['main' => $this->getTable('swissup_seourls_category_label')],
                    ['store_id', 'value']
                )
                ->where('entity_id = ?', $categoryId);
            $this->memoized[$categoryId] = $connection->fetchAssoc($select);
        }

        return $this->memoized[$categoryId];
    }
}

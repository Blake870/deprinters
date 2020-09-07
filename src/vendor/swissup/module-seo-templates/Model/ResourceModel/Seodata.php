<?php

namespace Swissup\SeoTemplates\Model\ResourceModel;

class Seodata extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * {@inheritdoc}
     */
    protected $_serializableFields = ['metadata' => ['', []]];

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('swissup_seotemplates_data', 'id');
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return \Magento\Framework\DB\Select
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        if ($field == 'entity_id') {
            if ($object->hasData('entity_type')) {
                $field = $this->getConnection()
                    ->quoteIdentifier(
                        sprintf('%s.%s', $this->getMainTable(), 'entity_type')
                    );
                $select->where($field . '=?', $object->getData('entity_type'));
            }

            if ($object->hasData('store_id')) {
                $field = $this->getConnection()
                    ->quoteIdentifier(
                        sprintf('%s.%s', $this->getMainTable(), 'store_id')
                    );
                $select->where($field . '=?', $object->getData('store_id'));
            }
        }

        return $select;
    }

    /**
     * Delete all generated data for entity types in $entityTypes
     *
     * @param  array  $entityTypes
     * @return void
     */
    public function deleteGenerated($entityTypes = [])
    {
        try {
            $connection = $this->getConnection();
            $connection->beginTransaction();
            $connection->delete(
                $this->getTable('swissup_seotemplates_data'),
                'entity_type in (' . implode(',', $entityTypes) . ')'
            );
            $connection->commit();
        } catch (\Exception $exception) {
            $connection->rollback();
        }
    }
}

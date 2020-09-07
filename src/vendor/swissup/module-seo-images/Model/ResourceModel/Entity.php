<?php

namespace Swissup\SeoImages\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Entity extends AbstractDb
{
    /**
     * {@inheritdoc}
     */
    protected $_isPkAutoIncrement = false;

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('swissup_seoimages', 'file_name');
    }

    /**
     * {@inheritdoc}
     */
    public function load(
        \Magento\Framework\Model\AbstractModel $object,
        $value,
        $field = null
    ) {
        // All fields in table has MAX_LENGTH = 255.
        $value = substr($value, 0, 255);
        return parent::load($object, $value, $field);
    }
}

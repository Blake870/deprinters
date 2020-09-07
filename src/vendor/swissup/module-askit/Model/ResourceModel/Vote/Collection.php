<?php
namespace Swissup\Askit\Model\ResourceModel\Vote;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Swissup\Askit\Model\Vote', 'Swissup\Askit\Model\ResourceModel\Vote');
    }
}

<?php

namespace Swissup\SeoTemplates\Model\ResourceModel;

class Log extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('swissup_seotemplates_log', 'id');
    }
}

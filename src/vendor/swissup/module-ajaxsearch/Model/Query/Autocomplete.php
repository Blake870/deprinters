<?php
namespace Swissup\Ajaxsearch\Model\Query;

use Magento\Search\Model\ResourceModel\Query\Collection;

/**
 * Search query model
 */
class Autocomplete extends \Swissup\Ajaxsearch\Model\Query
{
    /**
     * Retrieve collection of suggest queries
     *
     * @return QueryCollection
     */
    protected function _getSuggestCollection()
    {
        return $this->_queryCollectionFactory
            ->setInstanceName(Collection::class)
            ->create()
            ->setStoreId($this->getStoreId())
            ->setQueryFilter($this->getQueryText());
    }
}

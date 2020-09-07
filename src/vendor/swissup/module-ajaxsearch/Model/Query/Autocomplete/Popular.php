<?php
namespace Swissup\Ajaxsearch\Model\Query\Autocomplete;

use Magento\Search\Model\ResourceModel\Query\Collection;

/**
 * Search query model
 */
class Popular extends \Swissup\Ajaxsearch\Model\Query
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
            ->setPopularQueryFilter($this->getStoreId());
            ;
    }
}

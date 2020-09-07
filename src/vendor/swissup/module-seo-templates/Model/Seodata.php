<?php

namespace Swissup\SeoTemplates\Model;

class Seodata extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Swissup\SeoTemplates\Model\ResourceModel\Seodata::class);
    }

    public function loadByEntityId($entityId, $entityType, $storeId)
    {
        $this->setEntityType($entityType)
            ->setStoreId($storeId)
            ->setEntityId($entityId);
        return $this->load($entityId, 'entity_id');
    }

    /**
     * Delete all generated data for entity types in $entityTypes
     *
     * @param  array  $entityTypes
     * @return $this
     */
    public function deleteGenerated($entityTypes = [])
    {
        $this->getResource()->deleteGenerated($entityTypes);
        return $this;
    }
}

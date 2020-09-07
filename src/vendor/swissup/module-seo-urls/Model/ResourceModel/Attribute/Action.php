<?php

namespace Swissup\SeoUrls\Model\ResourceModel\Attribute;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\DataObject;

class Action extends AbstractDb
{
    /**
     * @var \Swissup\SeoUrls\Model\Attribute\AdvancedFactory
     */
    protected $attributeAdvancedFactory;
    /**
     * memorized in-url attribute label data
     *
     * @var array
     */
    protected $inUrlLabel;

    /**
     * Memorized attribute ID for attribuet code
     *
     * @var array
     */
    protected $attributeId;

    /**
     * @var array
     */
    private $cachedData = [];

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Swissup\SeoUrls\Model\Attribute\AdvancedFactory  $attributeAdvancedFactory
     * @param string                                            $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Swissup\SeoUrls\Model\Attribute\AdvancedFactory $attributeAdvancedFactory,
        $connectionName = null
    ) {
        $this->attributeAdvancedFactory = $attributeAdvancedFactory;
        parent::__construct($context, $connectionName);
    }

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('eav_attribute', 'attribute_id');
    }

    /**
     * Update in-url labels for attribute object $attribute
     *
     * @param  \Magento\Framework\DataObject $attribute
     * @param  array                         $newLabels
     * @param  bool                          $clearMemorized
     */
    public function updateInUrlLabels(
        \Magento\Framework\DataObject $attribute,
        array $newLabels = [],
        $clearMemorized = true
    ) {
        $newLabels = array_filter($newLabels);
        $oldLabels = [];
        foreach ($this->getInUrlLabels($attribute) as $label) {
            $oldLabels[$label['store_id']] = $label['value'];
        }

        $table = $this->getTable('swissup_seourls_attribute_label');
        $insert = array_diff($newLabels, $oldLabels);
        $delete = array_diff($oldLabels, $newLabels);
        if ($delete) {
            $where = array(
                'attribute_id = ?' => (int) $attribute->getId(),
                'store_id IN (?)' => array_keys($delete)
            );
            $this->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = [];
            foreach ($insert as $storeId => $value) {
                $data[] = [
                    'attribute_id'  => (int) $attribute->getId(),
                    'store_id' => (int) $storeId,
                    'value' => $value
                ];
            }

            $this->getConnection()->insertMultiple($table, $data);
        }

        if ($clearMemorized) {
            $this->clearMemorizedInUrlLabels();
        }
    }

    /**
     * Get in_URL values for $originalValue of attribute with ID $attributeId
     *
     * @param  int    $attributeId
     * @param  string $originalValue
     * @param  array  $newValues
     */
    public function updateInUrlValues(
        $attributeId,
        $originalValue,
        array $newValues = []
    ) {
        $newValues = array_filter($newValues);
        $oldValues = [];
        foreach ($this->getInUrlValues($attributeId, $originalValue) as $value) {
            $oldValues[$value['store_id']] = $value['url_value'];
        }
        $this->getInUrlValues($attributeId, $originalValue);

        $table = $this->getTable('swissup_seourls_attribute_value');
        $insert = array_diff($newValues, $oldValues);
        $delete = array_diff($oldValues, $newValues);
        if ($delete) {
            $where = [
                'attribute_id = ?' => (int) $attributeId,
                'original_value = ?' => $originalValue,
                'store_id IN (?)' => array_keys($delete)
            ];
            $this->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = [];
            foreach ($insert as $storeId => $value) {
                $data[] = [
                    'attribute_id'  => (int) $attributeId,
                    'store_id' => (int) $storeId,
                    'original_value' => $originalValue,
                    'url_value' => $value
                ];
            }

            $this->getConnection()->insertMultiple($table, $data);
        }

        $key = "value_{$attributeId}_{$originalValue}";
        unset($this->cachedData[$key]);
    }

    /**
     * Read in-url attribute labels from DB
     *
     * @param  int   $attributeId
     * @return array
     */
    public function getInUrlLabels(\Magento\Framework\DataObject $attribute)
    {
        $this->memorizeInUrlLabels();
        $attributeId = 0;
        if ($attribute->getId()) {
            // attribute ID passed - use simple select
            $attributeId = $attribute->getId();
        } elseif ($attribute->getAttributeCode()) {
            // no ID but there is code - join eav attribute table
            $attributeId = $this->getAttributeId($attribute->getAttributeCode());
        }

        return isset($this->inUrlLabel[$attributeId])
            ? $this->inUrlLabel[$attributeId]
            : [];
    }

    /**
     * Read in_URL values for $originalValue of attribute with ID $attributeId
     *
     * @param  int    $attributeId
     * @param  string $originalValue
     * @return array
     */
    public function getInUrlValues($attributeId, $originalValue)
    {
        $key = "value_{$attributeId}_{$originalValue}";
        if (!isset($this->cachedData[$key])) {
            $select = $this->getConnection()->select()
                ->from(
                    $this->getTable('swissup_seourls_attribute_value'),
                    ['store_id', 'url_value']
                )
                ->where('attribute_id = ?', (int)$attributeId)
                ->where('original_value = ?', $originalValue);
            $this->cachedData[$key] = $this->getConnection()->fetchAssoc($select);
        }

        return $this->cachedData[$key];
    }

    /**
     * Find ID for attribute code
     *
     * @param  string $attributeCode
     * @return int
     */
    private function getAttributeId($attributeCode)
    {
        return isset($this->attributeId[$attributeCode])
            ? (int)$this->attributeId[$attributeCode]
            : 0;
    }

    /**
     * @return void
     */
    private function memorizeInUrlLabels()
    {
        if (isset($this->inUrlLabel)) {
            return;
        }

        unset($this->attributeId);
        $select = $this->getConnection()->select()
                ->from(
                    ['main' => $this->getTable('swissup_seourls_attribute_label')],
                    ['attribute_id', 'store_id', 'value']
                )
                ->joinLeft(
                    ['eav' => $this->getMainTable()],
                    'eav.attribute_id = main.attribute_id',
                    ['attribute_code']
                );
        foreach ($this->getConnection()->fetchAll($select) as $row) {
            $this->inUrlLabel[$row['attribute_id']][$row['store_id']] = [
                'store_id' => $row['store_id'],
                'value' => $row['value']
            ];
            $this->attributeId[$row['attribute_code']] = $row['attribute_id'];
        }
    }

    /**
     * @return void
     */
    private function clearMemorizedInUrlLabels()
    {
        unset($this->inUrlLabel);
    }

    /**
     * Get advanced properties values
     *
     * @param  \Magento\Framework\DataObject $attribute
     * @return array
     */
    public function getAdvancedProps(\Magento\Framework\DataObject $attribute)
    {
        $key = "advanced_properties_{$attribute->getId()}";
        if (!isset($this->cachedData[$key])) {
            $attributeAdvanced = $this->attributeAdvancedFactory->create();
            $attributeAdvanced->load($attribute->getId());
            $this->cachedData[$key] = $attributeAdvanced->getData();
        }

        return $this->cachedData[$key];
    }

    /**
     * Update advanced properties for attribute
     *
     * @param  \Magento\Framework\DataObject $attribute
     * @param  array                         $newValues
     */
    public function updateAdvacedProps(
        \Magento\Framework\DataObject $attribute,
        array $newValues
    ) {
        $attributeAdvanced = $this->attributeAdvancedFactory->create();
        $attributeAdvanced->load($attribute->getId())
            ->addData($newValues)
            ->setId($attribute->getId())
            ->save();
        $key = "advanced_properties_{$attribute->getId()}";
        unset($this->cachedData[$key]);
    }
}

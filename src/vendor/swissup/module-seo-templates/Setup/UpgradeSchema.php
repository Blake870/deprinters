<?php

namespace Swissup\SeoTemplates\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Swissup\SeoTemplates\Model\ResourceModel\Seodata\CollectionFactory;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var array
     */
    private $columnsToDrop = ['meta_title', 'meta_description', 'meta_keywords'];

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.5.0', '<')) {
            $this->alterSeoDataTable($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param  SchemaSetupInterface $setup
     */
    protected function alterSeoDataTable(SchemaSetupInterface $setup)
    {
        // Add new column with serialized metadata.
        $setup->getConnection()->addColumn(
            $setup->getTable('swissup_seotemplates_data'),
            'metadata',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => false,
                'comment' => 'Meta Data'
            ]
        );

        // Fill new column with data
        $collection = $this->collectionFactory->create();
        foreach ($collection as $item) {
            $metadata = array_filter(
                $item->getData(),
                [$this, 'isDataToMove'],
                ARRAY_FILTER_USE_BOTH
            );
            $item->setMetadata($metadata)->save();
        }


        // Remove old columns.
        foreach ($this->columnsToDrop as $column) {
            $setup->getConnection()->dropColumn(
                $setup->getTable('swissup_seotemplates_data'),
                $column
            );
        }
    }

    /**
     * Check if data should be moved to new field.
     *
     * @param  string  $value
     * @param  string  $key
     * @return boolean
     */
    public function isDataToMove($value, $key) {
        return $value !== null && in_array($key, $this->columnsToDrop);
    }
}

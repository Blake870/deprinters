<?php

 namespace Swissup\SeoCrossLinks\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Upgrade the Cms module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->createMainTables($setup);
        }
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return $this
     */
    protected function createMainTables(SchemaSetupInterface $setup)
    {
        $setup->startSetup();

        /**
         * Create table 'swissup_seocrosslinks_link'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('swissup_seocrosslinks_link')
        )->addColumn(
            'link_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Link ID'
        )->addColumn(
            'keyword',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Keyword'
        )->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Title'
        )->addColumn(
            'url_destination',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => true, 'default' => \Swissup\SeoCrossLinks\Model\Link::URL_DESTINATION_CUSTOM],
            'URL Destination'
        )->addColumn(
            'url_entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            'URL Entity ID'
        )->addColumn(
            'url_path',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'URL Path'
        )->addColumn(
            'replacement_count',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Replacement Count'
        )->addColumn(
            'search_in',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Search in'
        )->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Is Link Active'
        )->addColumn(
            'class',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Style'
        );
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'cms_store'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('swissup_seocrosslinks_link_store')
        )->addColumn(
            'link_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'primary' => true],
            'Link ID'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Store ID'
        )->addIndex(
            $setup->getIdxName('swissup_seocrosslinks_link_store', ['store_id']),
            ['store_id']
        )->addForeignKey(
            $setup->getFkName('swissup_seocrosslinks_link_store', 'link_id', 'swissup_seocrosslinks_link', 'link_id'),
            'link_id',
            $setup->getTable('swissup_seocrosslinks_link'),
            'link_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $setup->getFkName('swissup_seocrosslinks_link_store', 'store_id', 'store', 'store_id'),
            'store_id',
            $setup->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );
        $setup->getConnection()->createTable($table);
        return $this;
    }
}

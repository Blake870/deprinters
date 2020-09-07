<?php
namespace Swissup\Attributepages\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /**
         * Create table 'swissup_attributepages_entity'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('swissup_attributepages_entity'))
            ->addColumn('entity_id', Table::TYPE_INTEGER, null, [
                'identity' => true,
                'nullable' => false,
                'unsigned' => true,
                'primary' => true
            ], 'Entity ID')
            ->addColumn('attribute_id', Table::TYPE_SMALLINT, 5, [
                'unsigned'  => true,
                'nullable'  => true,
                'default'  => NULL,
            ], 'Attribute Id')
            ->addColumn('option_id', Table::TYPE_INTEGER, 10, [
                'unsigned'  => true,
                'nullable'  => true,
                'default'  => NULL,
            ], 'Option Id')
            ->addColumn('name', Table::TYPE_TEXT, 64, [
                'nullable'  => false,
            ], 'Name')
            ->addColumn('identifier', Table::TYPE_TEXT, 255, [
                'nullable'  => false,
            ], 'Identifier')
            ->addColumn('title', Table::TYPE_TEXT, 255, [
                'nullable'  => true,
                'default'  => NULL,
            ], 'Title')
            ->addColumn('page_title', Table::TYPE_TEXT, 255, [
                'nullable'  => false,
                'default'  => '',
            ], 'Page Title')
            ->addColumn('content', Table::TYPE_TEXT, null, [
            ], 'Content')
            ->addColumn('image', Table::TYPE_TEXT, 255, [
                'nullable'  => true,
                'default'  => NULL,
            ], 'Image')
            ->addColumn('thumbnail', Table::TYPE_TEXT, 255, [
                'nullable'  => true,
                'default'  => NULL,
            ], 'Thumbnail')
            ->addColumn('meta_keywords', Table::TYPE_TEXT, null, [
            ], 'Meta Keywords')
            ->addColumn('meta_description', Table::TYPE_TEXT, null, [
            ], 'Meta Description')
            ->addColumn('display_settings', Table::TYPE_TEXT, null, [
            ], 'Display Settings')
            ->addColumn('root_template', Table::TYPE_TEXT, 255, [
                'nullable'  => true,
                'default'  => NULL,
            ], 'Root Template')
            ->addColumn('layout_update_xml', Table::TYPE_TEXT, null, [
            ], 'Layout Update Xml')
            ->addColumn('use_for_attribute_page', Table::TYPE_SMALLINT, 6, [
                'nullable'  => false,
                'default'  => 1,
            ], 'Use For Attribute Page')
            ->addColumn('use_for_product_page', Table::TYPE_SMALLINT, 6, [
                'nullable'  => false,
                'default'  => 0,
            ], 'Use For Product Page')
            ->addColumn('excluded_option_ids', Table::TYPE_TEXT, null, [
            ], 'Excluded Option Ids')
            ->addIndex($setup->getIdxName('swissup_attributepages_entity', ['attribute_id']),
                ['attribute_id'])
            ->addIndex($setup->getIdxName('swissup_attributepages_entity', ['option_id']),
                ['option_id'])
            ->addIndex($setup->getIdxName('swissup_attributepages_entity', ['identifier']),
                ['identifier'])
            ->addIndex($setup->getIdxName('swissup_attributepages_entity', ['title']),
                ['title'])
            ->addForeignKey($installer->getFkName('swissup_attributepages_entity', 'attribute_id', 'eav_attribute', 'attribute_id'),
                'attribute_id', $installer->getTable('eav_attribute'), 'attribute_id',
            Table::ACTION_CASCADE, Table::ACTION_CASCADE)
            ->addForeignKey($installer->getFkName('swissup_attributepages_entity', 'option_id', 'eav_attribute_option', 'option_id'),
                'option_id', $installer->getTable('eav_attribute_option'), 'option_id',
            Table::ACTION_CASCADE, Table::ACTION_CASCADE)
            ->setComment('Swissup Attributepages Entity Table');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'swissup_attributepages_store'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('swissup_attributepages_store')
        )->addColumn(
            'entity_id',
            Table::TYPE_INTEGER,
            null,
            [ 'nullable' => false, 'unsigned' => true, 'primary' => true ],
            'Entity ID'
        )->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Store ID'
        )->addIndex(
            $installer->getIdxName('swissup_attributepages_store', ['store_id']),
            ['store_id']
        )->addForeignKey(
            $installer->getFkName('swissup_attributepages_store', 'entity_id', 'swissup_attributepages_entity', 'entity_id'),
            'entity_id',
            $installer->getTable('swissup_attributepages_entity'),
            'entity_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('swissup_attributepages_store', 'store_id', 'store', 'store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Swissup Attribute Pages To Store Linkage Table'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}

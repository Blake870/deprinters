<?php
namespace Swissup\Easybanner\Setup;

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
         * Create table 'swissup_easybanner_banner'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('swissup_easybanner_banner'))
            ->addColumn('banner_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
            ], 'Banner Id')
            ->addColumn('identifier', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 64, [
                'nullable'  => false,
            ], 'Identifier')
            ->addColumn('type', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 3, [
                'unsigned'  => true,
                'nullable'  => false,
                'default'  => 1,
            ], 'Type')
            ->addColumn('sort_order', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 3, [
                'unsigned'  => true,
                'nullable'  => false,
                'default'  => 100,
            ], 'Sort Order')
            ->addColumn('title', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, [
                'nullable'  => false,
            ], 'Title')
            ->addColumn('url', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [
                'nullable'  => false,
                'default'  => '',
            ], 'Url')
            ->addColumn('image', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [
                'nullable'  => false,
            ], 'Image')
            ->addColumn('html', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, [
                'nullable'  => false,
            ], 'Html')
            ->addColumn('status', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 1, [
                'unsigned'  => true,
                'nullable'  => false,
                'default'  => 1,
            ], 'Status')
            ->addColumn('mode', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, [
                'nullable'  => false,
                'default'  => '',
            ], 'Mode')
            ->addColumn('target', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, [
                'nullable'  => false,
            ], 'Target')
            ->addColumn('hide_url', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 1, [
                'unsigned'  => true,
                'nullable'  => false,
                'default'  => 0,
            ], 'Hide Url')
            ->addColumn('conditions_serialized', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, [
                'nullable'  => false,
            ], 'Conditions Serialized')
            ->addColumn('resize_image', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 6, [
                'nullable'  => false,
                'default'  => 0,
            ], 'Resize Image')
            ->addColumn('width', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 5, [
                'unsigned'  => true,
                'default'  => 0,
            ], 'Width')
            ->addColumn('height', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 5, [
                'unsigned'  => true,
                'default'  => 0,
            ], 'Height')
            ->addColumn('retina_support', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 6, [
                'nullable'  => false,
                'default'  => 1,
            ], 'Retina Support')
            ->addColumn('background_color', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 11, [
                'nullable'  => false,
                'default'  => 255,255,255,
            ], 'Background Color')
            ->addColumn('class_name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 256, [
                'nullable'  => false,
                'default'  => '',
            ], 'Class Name')
        ;
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'swissup_easybanner_placeholder'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('swissup_easybanner_placeholder'))
            ->addColumn('placeholder_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
            ], 'Placeholder Id')
            ->addColumn('name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 64, [
                'nullable'  => false,
            ], 'Name')
            ->addColumn('status', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 1, [
                'unsigned'  => true,
                'nullable'  => false,
                'default'  => 1,
            ], 'Status')
            ->addColumn('limit', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 3, [
                'unsigned'  => true,
                'nullable'  => false,
            ], 'Limit')
            ->addColumn('mode', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, [
                'nullable'  => false,
                'default'  => '',
            ], 'Mode')
            ->addColumn('banner_offset', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 3, [
                'unsigned'  => true,
                'nullable'  => false,
                'default'  => 0,
            ], 'Banner Offset')
            ->addColumn('sort_mode', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, [
                'nullable'  => false,
                'default'  => '',
            ], 'Sort Mode');

        $installer->getConnection()->createTable($table);

        /**
         * Create table 'swissup_easybanner_banner_placeholder'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('swissup_easybanner_banner_placeholder'))
            ->addColumn('banner_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
            ], 'Banner Id')
            ->addColumn('placeholder_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
            ], 'Placeholder Id')
            ->addIndex($installer->getIdxName('swissup_easybanner_banner_placeholder', ['placeholder_id']),
                ['placeholder_id'])
            ->addForeignKey($installer->getFkName('swissup_easybanner_banner_placeholder', 'banner_id', 'swissup_easybanner_banner', 'banner_id'),
                'banner_id', $installer->getTable('swissup_easybanner_banner'), 'banner_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE, \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->addForeignKey($installer->getFkName('swissup_easybanner_placeholder', 'placeholder_id', 'swissup_easybanner_placeholder', 'placeholder_id'),
                'placeholder_id', $installer->getTable('swissup_easybanner_placeholder'), 'placeholder_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE, \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE);

        $installer->getConnection()->createTable($table);

        /**
         * Create table 'swissup_easybanner_banner_statistic'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('swissup_easybanner_banner_statistic'))
            ->addColumn('id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
            ], 'Id')
            ->addColumn('banner_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [
                'unsigned'  => true,
                'nullable'  => false,
            ], 'Banner Id')
            ->addColumn('date', \Magento\Framework\DB\Ddl\Table::TYPE_DATE, null, [
                'nullable'  => false,
            ], 'Date')
            ->addColumn('display_count', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, [
                'unsigned'  => true,
                'nullable'  => false,
                'default'  => 0,
            ], 'Display Count')
            ->addColumn('clicks_count', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, [
                'unsigned'  => true,
                'nullable'  => false,
                'default'  => 0,
            ], 'Clicks Count')
            ->addIndex($installer->getIdxName('swissup_easybanner_banner_statistic_id', ['id']),
                ['id'])
            ->addIndex($installer->getIdxName('swissup_easybanner_banner_statistic', ['banner_id']),
                ['banner_id'])
            ->addForeignKey($installer->getFkName('swissup_easybanner_banner_statistic', 'banner_id', 'swissup_easybanner_banner', 'banner_id'),
                'banner_id', $installer->getTable('swissup_easybanner_banner'), 'banner_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE, \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
        ;
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'swissup_easybanner_banner_store'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('swissup_easybanner_banner_store'))
            ->addColumn('banner_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
            ], 'Banner Id')
            ->addColumn('store_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 5, [
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
            ], 'Store Id')
            ->addIndex($installer->getIdxName('swissup_easybanner_banner_store', ['store_id']),
                ['store_id'])
            ->addIndex($installer->getIdxName('swissup_easybanner_banner_store_banner_id', ['banner_id']),
                ['banner_id'])
            ->addForeignKey($installer->getFkName('swissup_easybanner_banner_store', 'banner_id', 'swissup_easybanner_banner', 'banner_id'),
                'banner_id', $installer->getTable('swissup_easybanner_banner'), 'banner_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE, \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->addForeignKey($installer->getFkName('swissup_easybanner_banner_store', 'store_id', 'store', 'store_id'),
                'store_id', $installer->getTable('store'), 'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE, \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
        ;
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}

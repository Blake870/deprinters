<?php
namespace Swissup\EasySlide\Setup;

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
         * Create table 'swissup_easyslide_slider'
         */
         $table = $installer->getConnection()
            ->newTable($installer->getTable('swissup_easyslide_slider'))
            ->addColumn('slider_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, array(
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
            ), 'Slider Id')
            ->addColumn('identifier', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 60, array(
                'nullable'  => false,
            ), 'Identifier')
            ->addColumn('title', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(
                'nullable'  => false,
            ), 'Title')
            ->addColumn('slider_config', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, array(
                'nullable'  => false,
            ), 'Slider Config')
            ->addColumn('is_active', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 1, array(
                'nullable'  => false,
                'default'  => 1,
            ), 'Is Active');
        $installer->getConnection()->createTable($table);
        /**
         * Create table 'swissup_easyslide_slides'
         */
         $table = $installer->getConnection()
            ->newTable($installer->getTable('swissup_easyslide_slides'))
            ->addColumn('slide_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, array(
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
            ), 'Slide Id')
            ->addColumn('slider_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, array(
                'nullable'  => false,
            ), 'Slider Id')
            ->addColumn('title', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(
                'nullable'  => false,
            ), 'Title')
            ->addColumn('image', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(
                'nullable'  => false,
            ), 'Image')
            ->addColumn('description', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, array(
            ), 'Description')
            ->addColumn('desc_position', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 45, array(
                'nullable'  => true,
                'default'  => NULL,
            ), 'Desc Position')
            ->addColumn('desc_background', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 45, array(
                'nullable'  => true,
                'default'  => NULL,
            ), 'Desc Background')
            ->addColumn('url', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(
                'nullable'  => true,
                'default'  => NULL,
            ), 'Url')
            ->addColumn('target', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 60, array(
                'nullable'  => true,
                'default'  => NULL,
            ), 'Target')
            ->addColumn('sort_order', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, array(
                'nullable'  => true,
                'default'  => NULL,
            ), 'Sort Order')
            ->addColumn('is_active', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, array(
                'nullable'  => true,
                'default'  => NULL,
            ), 'Is Active')
            ->addIndex($installer->getIdxName('swissup_easyslide_slides', array('slider_id')),
                array('slider_id'))
            ->addForeignKey($installer->getFkName('swissup_easyslide_slides', 'slider_id', 'swissup_easyslide_slider', 'slider_id'),
                'slider_id', $installer->getTable('swissup_easyslide_slider'), 'slider_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE, \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
        ;
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}

<?php
namespace Swissup\SeoTemplates\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * Installs DB schema for a module
     *
     * @param  \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param  \Magento\Framework\Setup\ModuleContextInterface $context
     * @return viod
     */
    public function install(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        /**
         * Create table 'swissup_seotemplates_template'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('swissup_seotemplates_template'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary'  => true
                ],
                'Template Id'
            )
            ->addColumn(
                'name',
                Table::TYPE_TEXT,
                255,
                [
                    'nullable'  => true
                ],
                'Template Name'
            )
            ->addColumn(
                'entity_type',
                Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned'  => true,
                    'nullable'  => false
                ],
                'Entity Type'
            )
            ->addColumn(
                'data_name',
                Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned'  => true,
                    'nullable'  => false
                ],
                'SEO Data Name'
            )
            ->addColumn(
                'priority',
                Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned'  => true,
                    'nullable'  => false
                ],
                'Priority'
            )
            ->addColumn(
                'status',
                Table::TYPE_BOOLEAN,
                null,
                [],
                'Status'
            )
            ->addColumn(
                'conditions_serialized',
                Table::TYPE_TEXT,
                null,
                [],
                'Conditions Serialized'
            )
            ->addColumn('template',
                Table::TYPE_TEXT,
                null,
                [],
                'Template'
            )
            ->addColumn(
                'update_time',
                Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE
                ],
                'Modified at'
            )
            ->setComment(
                'SEO Templates - template data'
            );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'swissup_seotemplates_store'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('swissup_seotemplates_store'))
            ->addColumn(
                'template_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned'  => true,
                    'nullable'  => false,
                    'primary'   => true
                ],
                'Template ID'
            )
            ->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned'  => true,
                    'nullable'  => false,
                    'primary'   => true,
                ],
                'Store ID'
            )
            ->addIndex(
                $installer->getIdxName('swissup_seotemplates_store', array('store_id')),
                array('store_id')
            )
            ->addForeignKey(
                $installer->getFkName(
                    'swissup_seotemplates_store',
                    'template_id',
                    'swissup_seotemplates_template',
                    'id'
                ),
                'template_id',
                $installer->getTable('swissup_seotemplates_template'),
                'id',
                Table::ACTION_CASCADE,
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName(
                    'swissup_seotemplates_store',
                    'store_id',
                    'store',
                    'store_id'
                ),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE,
                Table::ACTION_CASCADE
            )
            ->setComment(
                'SEO Templates - template to store link'
            );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'swissup_seotemplates_data'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('swissup_seotemplates_data'))
            ->addColumn(
                'id',
                Table::TYPE_BIGINT,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary'  => true
                ],
                'Id'
            )
            ->addColumn(
                'entity_type',
                Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned'  => true,
                    'nullable'  => false
                ],
                'Entity Type'
            )
            ->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned'  => true,
                    'nullable'  => false
                ],
                'Entity Id'
            )
            ->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned'  => true,
                    'nullable'  => false
                ],
                'Store ID'
            )
            ->addColumn(
                'meta_title',
                Table::TYPE_TEXT,
                null,
                [],
                'Meta Title'
            )
            ->addColumn(
                'meta_description',
                Table::TYPE_TEXT,
                null,
                [],
                'Meta Description'
            )
            ->addColumn(
                'meta_keywords',
                Table::TYPE_TEXT,
                null,
                [],
                'Meta Keywords'
            )
            ->addForeignKey(
                $installer->getFkName(
                    'swissup_seotemplates_data',
                    'store_id',
                    'store',
                    'store_id'
                ),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE,
                Table::ACTION_CASCADE
            )
            ->setComment(
                'SEO Templates -  Data'
            );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'swissup_seotemplates_log'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('swissup_seotemplates_log'))
            ->addColumn(
                'id',
                Table::TYPE_BIGINT,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary'  => true
                ],
                'Id'
            )
            ->addColumn(
                'template_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned'  => true,
                    'nullable'  => false
                ],
                'Template Id'
            )
            ->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned'  => true,
                    'nullable'  => false
                ],
                'Entity Id'
            )
            ->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned'  => true,
                    'nullable'  => false,
                ],
                'Store ID'
            )
            ->addColumn(
                'generated_value',
                Table::TYPE_TEXT,
                null,
                [],
                'Generated Value'
            )
            ->addColumn(
                'generation_time',
                Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE
                ],
                'Generated at'
            )
            ->addForeignKey(
                $installer->getFkName(
                    'swissup_seotemplates_log',
                    'store_id',
                    'store',
                    'store_id'
                ),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE,
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName('swissup_seotemplates_log',
                    'template_id',
                    'swissup_seotemplates_template',
                    'id'
                ),
                'template_id',
                $installer->getTable('swissup_seotemplates_template'),
                'id',
                Table::ACTION_CASCADE,
                Table::ACTION_CASCADE
            )
            ->setComment('SEO Templates -  Log');
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}

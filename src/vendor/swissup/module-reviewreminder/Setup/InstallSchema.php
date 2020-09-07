<?php
namespace Swissup\Reviewreminder\Setup;

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
         * Create table 'swissup_reviewreminder_entity'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('swissup_reviewreminder_entity'))
            ->addColumn('entity_id', Table::TYPE_INTEGER, null, [
                'identity' => true,
                'nullable' => false,
                'unsigned' => true,
                'primary' => true
            ], 'Entity ID')
            ->addColumn('order_id', Table::TYPE_INTEGER, null, ['nullable' => false, 'unsigned' => true], 'Order id')
            ->addColumn('order_date',
                Table::TYPE_TIMESTAMP, null,
                [
                    'nullable' => false,
                    'default' => Table::TIMESTAMP_INIT
                ], 'Order Date'
            )
            ->addColumn('customer_email', Table::TYPE_TEXT, 255, ['nullable' => false], 'Customer Email')
            ->addColumn('status', Table::TYPE_SMALLINT, null, ['nullable' => false], 'Reminder status')
            ->addColumn('hash', Table::TYPE_TEXT, 16, ['nullable' => false, 'default' => 1], 'Hash')
            ->addIndex(
                $setup->getIdxName(
                    $installer->getTable('swissup_reviewreminder_entity'),
                    ['customer_email'],
                    AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                ['customer_email'],
                ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
            )
            ->setComment('Swissup Review Reminder Entity Table');
        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}

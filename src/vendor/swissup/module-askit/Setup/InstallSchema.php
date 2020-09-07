<?php
//delete from setup_module where module='Swissup_Askit';
namespace Swissup\Askit\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $table = $installer->getConnection()
            ->newTable($installer->getTable('swissup_askit_item'))
            ->addColumn('id', Table::TYPE_INTEGER, 11, [
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
            ], 'Id')
            ->addColumn('parent_id', Table::TYPE_INTEGER, 10, [
                'unsigned'  => true,
                'nullable'  => true,
                'default'  => null,
            ], 'Parent Id')
            ->addColumn('item_type_id', Table::TYPE_SMALLINT, 5, [
                'unsigned'  => true,
                'nullable'  => false,
                'default'  => \Swissup\Askit\Api\Data\MessageInterface::TYPE_CATALOG_PRODUCT,
            ], 'Item Type Id')
            ->addColumn('item_id', Table::TYPE_INTEGER, 11, [
                'nullable'  => true,
                'default'  => null,
            ], 'Item Id')
            ->addColumn('store_id', Table::TYPE_SMALLINT, 5, [
                'unsigned'  => true,
                'nullable'  => false,
            ], 'Store Id')
            ->addColumn('customer_id', Table::TYPE_INTEGER, 10, [
                'unsigned'  => true,
                'nullable'  => true,
                'default'  => null,
            ], 'Customer Id')
            ->addColumn('customer_name', Table::TYPE_TEXT, 128, [
                'nullable'  => false,
                'default'  => '',
            ], 'Customer Name')
            ->addColumn('email', Table::TYPE_TEXT, 128, [
                'nullable'  => false,
                'default'  => '',
            ], 'Email')
            ->addColumn('text', Table::TYPE_TEXT, null, [
                'nullable'  => false,
            ], 'Text')
            ->addColumn('hint', Table::TYPE_SMALLINT, 6, [
                'nullable'  => false,
                'default'  => 0,
            ], 'Hint')
            ->addColumn('status', Table::TYPE_SMALLINT, 1, [
                'nullable'  => false,
                'default'  => 1,
            ], 'Status')
            ->addColumn('created_time', Table::TYPE_DATETIME, null, [
                'nullable'  => true,
                'default'  => null,
            ], 'Created Time')
            ->addColumn('update_time', Table::TYPE_DATETIME, null, [
                'nullable'  => true,
                'default'  => null,
            ], 'Update Time')
            ->addColumn('is_private', Table::TYPE_SMALLINT, 1, [
                'nullable'  => false,
                'default'  => 0,
            ], 'Private')
            ->addIndex(
                $installer->getIdxName('swissup_askit_item', ['item_id']),
                ['item_id']
            )
            ->addIndex(
                $installer->getIdxName('swissup_askit_item', ['customer_id']),
                ['customer_id']
            )
            ->addIndex(
                $installer->getIdxName('swissup_askit_item', ['store_id']),
                ['store_id']
            )
            ->addForeignKey(
                $installer->getFkName('swissup_askit_item', 'customer_id', 'customer_entity', 'entity_id'),
                'customer_id',
                $installer->getTable('customer_entity'),
                'entity_id',
                Table::ACTION_SET_NULL
            )
            ->addForeignKey(
                $installer->getFkName('swissup_askit_item', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )
            ->addIndex(
                $setup->getIdxName(
                    $installer->getTable('swissup_askit_item'),
                    ['text'],
                    AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                ['text'],
                ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
            );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()
            ->newTable($installer->getTable('swissup_askit_vote'))
            ->addColumn('id', Table::TYPE_INTEGER, 11, [
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
            ], 'Id')
            ->addColumn('item_id', Table::TYPE_INTEGER, 11, [
                'unsigned'  => true,
                'nullable'  => false,
            ], 'Item Id')
            ->addColumn('customer_id', Table::TYPE_INTEGER, 10, [
                'unsigned'  => true,
                'nullable'  => false,
            ], 'Customer Id')
            ->addIndex(
                $installer->getIdxName('swissup_askit_vote', ['item_id']),
                ['item_id']
            )
            ->addIndex(
                $installer->getIdxName('swissup_askit_vote', ['customer_id']),
                ['customer_id']
            )
            ->addForeignKey(
                $installer->getFkName('swissup_askit_vote', 'customer_id', 'customer_entity', 'entity_id'),
                'customer_id',
                $installer->getTable('customer_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName('swissup_askit_vote', 'item_id', 'swissup_askit_item', 'id'),
                'item_id',
                $installer->getTable('swissup_askit_item'),
                'id',
                Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}

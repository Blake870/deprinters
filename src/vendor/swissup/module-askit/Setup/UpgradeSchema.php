<?php
//delete from setup_module where module='Swissup_Askit';
namespace Swissup\Askit\Setup;

// use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $setup->startSetup();
        $connection = $setup->getConnection();

        //rename table swissup_askit_item => swissup_askit_message
        if (version_compare($context->getVersion(), '1.2.4', '<')) {
            $oldTableName = $installer->getTable('swissup_askit_item');
            $newTableName = $installer->getTable('swissup_askit_message');

            if ($installer->tableExists('swissup_askit_item')) {
                $oldForeignKeys = $connection->getForeignKeys($oldTableName);
                foreach ($oldForeignKeys as $foreignKey) {
                    $connection->dropForeignKey($oldTableName, $foreignKey['FK_NAME']);
                }

                $connection->dropIndex(
                    $setup->getTable('swissup_askit_item'),
                    $installer->getIdxName('swissup_askit_item', ['item_id'])
                );

                $connection->dropIndex(
                    $setup->getTable('swissup_askit_item'),
                    $installer->getIdxName('swissup_askit_item', ['customer_id'])
                );

                $connection->dropIndex(
                    $setup->getTable('swissup_askit_item'),
                    $installer->getIdxName('swissup_askit_item', ['store_id'])
                );

                $connection->dropIndex(
                    $setup->getTable('swissup_askit_item'),
                    $setup->getIdxName(
                        $installer->getTable('swissup_askit_item'),
                        ['text'],
                        AdapterInterface::INDEX_TYPE_FULLTEXT
                    )
                );

                $connection->renameTable($oldTableName, $newTableName);
            } else {
                $table = $installer->getConnection()
                    ->newTable($installer->getTable('swissup_askit_message'))
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
                        'default'  => 1,
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
                    ], 'Private');
                $installer->getConnection()->createTable($table);
            }

            $connection->addIndex(
                $installer->getTable('swissup_askit_message'),
                $installer->getIdxName('swissup_askit_message', ['item_id']),
                ['item_id']
            );
            $connection->addIndex(
                $installer->getTable('swissup_askit_message'),
                $installer->getIdxName('swissup_askit_message', ['customer_id']),
                ['customer_id']
            );
            $connection->addIndex(
                $installer->getTable('swissup_askit_message'),
                $installer->getIdxName('swissup_askit_message', ['store_id']),
                ['store_id']
            );
            $connection->addIndex(
                $installer->getTable('swissup_askit_message'),
                $setup->getIdxName(
                    $installer->getTable('swissup_askit_message'),
                    ['text'],
                    AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                ['text'],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            );

            $connection->addForeignKey(
                $installer->getFkName('swissup_askit_message', 'customer_id', 'customer_entity', 'entity_id'),
                $installer->getTable('swissup_askit_message'),
                'customer_id',
                $installer->getTable('customer_entity'),
                'entity_id',
                Table::ACTION_SET_NULL
            );
            $connection->addForeignKey(
                $installer->getFkName('swissup_askit_message', 'store_id', 'store', 'store_id'),
                $installer->getTable('swissup_askit_message'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            );
        }

        if (version_compare($context->getVersion(), '1.2.5', '<')) {
            if ($installer->tableExists('swissup_askit_vote')) {
                $voteTableName = $installer->getTable('swissup_askit_vote');
                $oldForeignKeys = $connection->getForeignKeys($voteTableName);
                foreach ($oldForeignKeys as $foreignKey) {
                    $connection->dropForeignKey($voteTableName, $foreignKey['FK_NAME']);
                }

                $connection->dropIndex(
                    $installer->getTable('swissup_askit_vote'),
                    $installer->getIdxName('swissup_askit_vote', ['message_id'])
                );

                $connection->dropIndex(
                    $installer->getTable('swissup_askit_vote'),
                    $installer->getIdxName('swissup_askit_vote', ['item_id'])
                );

                $connection->dropIndex(
                    $installer->getTable('swissup_askit_vote'),
                    $installer->getIdxName('swissup_askit_vote', ['customer_id'])
                );

                $connection->changeColumn(
                    $installer->getTable('swissup_askit_vote'),
                    'item_id',
                    'message_id',
                    [
                        'type' => Table::TYPE_INTEGER,
                        'length' => 11,
                        'unsigned'  => true,
                        'nullable'  => false,
                        'comment' => 'Message Id'
                    ]
                );
            } else {
                $table = $connection
                    ->newTable($installer->getTable('swissup_askit_vote'))
                    ->addColumn('id', Table::TYPE_INTEGER, 11, [
                        'identity'  => true,
                        'unsigned'  => true,
                        'nullable'  => false,
                        'primary'   => true,
                    ], 'Id')
                    ->addColumn('message_id', Table::TYPE_INTEGER, 11, [
                        'unsigned'  => true,
                        'nullable'  => false,
                    ], 'Message Id')
                    ->addColumn('customer_id', Table::TYPE_INTEGER, 10, [
                        'unsigned'  => true,
                        'nullable'  => false,
                    ], 'Customer Id');
                $connection->createTable($table);
            }

            $connection->addIndex(
                $installer->getTable('swissup_askit_vote'),
                $installer->getIdxName('swissup_askit_vote', ['message_id']),
                ['message_id']
            );
            $connection->addIndex(
                $installer->getTable('swissup_askit_vote'),
                $installer->getIdxName('swissup_askit_vote', ['customer_id']),
                ['customer_id']
            );

            $connection->addForeignKey(
                $installer->getFkName('swissup_askit_vote', 'message_id', 'swissup_askit_message', 'id'),
                $installer->getTable('swissup_askit_vote'),
                'message_id',
                $installer->getTable('swissup_askit_message'),
                'id',
                Table::ACTION_CASCADE
            );
            $connection->addForeignKey(
                $installer->getFkName('swissup_askit_vote', 'customer_id', 'customer_entity', 'entity_id'),
                $installer->getTable('swissup_askit_vote'),
                'customer_id',
                $installer->getTable('customer_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            );
        }

        $itemTableName = $installer->getTable('swissup_askit_item');
        $messageTableName = $installer->getTable('swissup_askit_message');
        if (version_compare($context->getVersion(), '1.2.6', '<')
            && !$connection->isTableExists($itemTableName)
        ) {
            $table = $connection
                ->newTable($itemTableName)
                ->addColumn('id', Table::TYPE_INTEGER, 11, [
                    'identity'  => true,
                    'unsigned'  => true,
                    'nullable'  => false,
                    'primary'   => true,
                    ], 'Id')
                ->addColumn('message_id', Table::TYPE_INTEGER, 11, [
                    'unsigned'  => true,
                    'nullable'  => true,
                    'default'   => null,
                    ], 'Massage Id')
                ->addColumn('item_id', Table::TYPE_INTEGER, 11, [
                    'unsigned'  => true,
                    'nullable'  => true,
                    'default'   => null,
                    ], 'Item Id (product_id, cms page id or category id)')
                ->addColumn('item_type_id', Table::TYPE_SMALLINT, 5, [
                    'unsigned'  => true,
                    'nullable'  => false,
                    'default'   => \Swissup\Askit\Api\Data\MessageInterface::TYPE_CATALOG_PRODUCT,
                    ], 'Item Type Id')
                ->addIndex(
                    $installer->getIdxName($itemTableName, ['message_id']),
                    ['message_id']
                )
                ->addForeignKey(
                    $installer->getFkName($itemTableName, 'message_id', 'tm_askit_message', 'id'),
                    'message_id',
                    $messageTableName,
                    'id',
                    Table::ACTION_CASCADE
                )
                ->setComment('Askit Assign Item Table');
            $connection->createTable($table);

            $select = $connection->select()->from($messageTableName);

            $messages = $connection->fetchAll($select);
            foreach ($messages as $id => $message) {
                if (!empty($message['item_id']) && !empty($message['item_type_id']) && !empty($message['id'])) {
                    $connection->insert($itemTableName, [
                        'message_id'   => $message['id'],
                        'item_id'      => $message['item_id'],
                        'item_type_id' => $message['item_type_id'],
                    ]);
                }
            }

            $connection->dropColumn($messageTableName, 'item_id');
            $connection->dropColumn($messageTableName, 'item_type_id');

            $connection->addIndex(
                $itemTableName,
                $installer->getIdxName(
                    'swissup_askit_item',
                    ['message_id', 'item_id', 'item_type_id'],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['message_id', 'item_id', 'item_type_id'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            );
        }

        $setup->endSetup();
    }
}

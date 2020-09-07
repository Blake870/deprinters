<?php
namespace Swissup\Easybanner\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

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

        $setup->startSetup();
        $connection = $setup->getConnection();

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            /**
             * Create table 'swissup_easybanner_banner_placeholder'
             */
            $table = $connection->newTable($setup->getTable('swissup_easybanner_placeholder_offset'))
                ->addColumn('id', Table::TYPE_BIGINT, null, [
                    'identity'  => true,
                    'unsigned'  => true,
                    'nullable'  => false,
                    'primary'   => true,
                ], 'Record Id')
                ->addColumn('placeholder_id', Table::TYPE_INTEGER, null, [
                    'unsigned'  => true,
                    'nullable'  => false,
                ], 'Placeholder Id')
                ->addColumn('banner_offset', Table::TYPE_SMALLINT, 3, [
                    'unsigned'  => true,
                    'nullable'  => false,
                    'default'  => 0,
                ], 'Banner Offset')
                ->addColumn(
                    'insert_time',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                    'Record Insert Time'
                )
                ->addIndex($setup->getIdxName('swissup_easybanner_placeholder_offset', ['placeholder_id']),
                    ['placeholder_id'])
                ->addForeignKey($setup->getFkName('swissup_easybanner_placeholder_offset', 'placeholder_id', 'swissup_easybanner_placeholder', 'placeholder_id'),
                    'placeholder_id', $setup->getTable('swissup_easybanner_placeholder'), 'placeholder_id',
                Table::ACTION_CASCADE, Table::ACTION_CASCADE);

            $connection->createTable($table);

            /**
             * Remove column 'banner_offset' from placeholder table
             */
            $connection->dropColumn($setup->getTable('swissup_easybanner_placeholder'), 'banner_offset');

            /**
             * Modify column 'id' in table with banner statistic
             */
            $connection->modifyColumn(
                $setup->getTable('swissup_easybanner_banner_statistic'),
                'id',
                [
                    'type' => Table::TYPE_BIGINT,
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'comment' => 'Id'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.2.0', '<')) {
            $setup->getConnection()
                ->addColumn(
                    $setup->getTable('swissup_easybanner_placeholder'),
                    'container',
                    [
                        'type' => Table::TYPE_TEXT,
                        'length' => 255,
                        'nullable' => false,
                        'default' => '',
                        'comment' => 'Parent Container'
                    ]
                );

            $setup->getConnection()
                ->addColumn(
                    $setup->getTable('swissup_easybanner_placeholder'),
                    'position',
                    [
                        'type' => Table::TYPE_TEXT,
                        'length' => 255,
                        'nullable' => false,
                        'default' => '',
                        'comment' => 'Position in Parent Container'
                    ]
                );

            $setup->getConnection()
                ->addColumn(
                    $setup->getTable('swissup_easybanner_placeholder'),
                    'additional_css_class',
                    [
                        'type' => Table::TYPE_TEXT,
                        'length' => 255,
                        'nullable' => false,
                        'default' => '',
                        'comment' => 'Additional CSS Classes'
                    ]
                );
        }

        $setup->endSetup();
    }
}

<?php

namespace Swissup\Navigationpro\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
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

        if (version_compare($context->getVersion(), '0.0.2', '<')) {
            $this->createMenuTable($setup);
            $this->createItemTable($setup);
        }

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->addItemSettings($setup);
        }

        if (version_compare($context->getVersion(), '1.2.0', '<')) {
            $this->addStyles($setup);
        }

        $setup->endSetup();
    }

    protected function createMenuTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable('swissup_navigationpro_menu'))
            ->addColumn(
                'menu_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'nullable' => false,
                    'primary'  => true,
                    'unsigned' => true,
                    'identity' => true,
                ],
                'Menu ID'
            )
            ->addColumn(
                'identifier',
                Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                ],
                'Menu Identifier'
            )
            ->addColumn(
                'is_active',
                Table::TYPE_SMALLINT,
                null,
                [
                    'nullable' => false,
                    'default'  => 1
                ],
                'Is Menu Active'
            )
            ->addColumn(
                'direction',
                Table::TYPE_TEXT,
                16,
                [],
                'Items Direction'
            )
            ->addColumn(
                'max_depth',
                Table::TYPE_SMALLINT,
                null,
                [
                    'nullable' => false,
                    'unsigned' => true,
                    'default' => 0,
                ],
                'Max Depth'
            )
            ->addColumn(
                'css_class',
                Table::TYPE_TEXT,
                null,
                [],
                'CSS Class'
            )
            ->addColumn(
                'dropdown_settings',
                Table::TYPE_TEXT,
                null,
                [],
                'Dropdown Settings'
            )
            ->setComment(
                'NavigationPro Menu Table'
            );
        $setup->getConnection()->createTable($table);
    }

    protected function createItemTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable('swissup_navigationpro_item'))
            ->addColumn(
                'item_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'nullable' => false,
                    'primary'  => true,
                    'unsigned' => true,
                    'identity' => true,
                ],
                'Item ID'
            )
            ->addColumn(
                'parent_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'nullable' => true,
                    'unsigned' => true,
                ],
                'Parent ID'
            )
            ->addColumn(
                'menu_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'nullable' => false,
                    'unsigned' => true,
                ],
                'Menu ID'
            )
            ->addColumn(
                'path',
                Table::TYPE_TEXT,
                255,
                [],
                'Tree Path'
            )
            ->addColumn(
                'position',
                Table::TYPE_INTEGER,
                null,
                [
                    'nullable' => false,
                    'unsigned' => true,
                    'default' => 1,
                ],
                'Item Position'
            )
            ->addColumn(
                'level',
                Table::TYPE_INTEGER,
                null,
                [
                    'nullable' => false,
                    'unsigned' => true,
                    'default'  => 0,
                ],
                'Tree Level'
            )
            ->addColumn(
                'is_active',
                Table::TYPE_SMALLINT,
                null,
                [
                    'nullable' => false,
                    'unsigned' => true,
                    'default'  => 1,
                ],
                'Is Item Active'
            )
            ->addColumn(
                'remote_entity_type',
                Table::TYPE_SMALLINT,
                null,
                [
                    'nullable' => true,
                    'unsigned' => true,
                    'default'  => null,
                ],
                'Remote Entity Type'
            )
            ->addColumn(
                'remote_entity_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'nullable' => true,
                    'unsigned' => true,
                    'default'  => null,
                ],
                'Remote Entity ID'
            )
            ->addColumn(
                'use_remote_data',
                Table::TYPE_SMALLINT,
                null,
                [
                    'nullable' => false,
                    'unsigned' => true,
                    'default'  => 1,
                ],
                'Use Remote Data'
            )
            ->addIndex(
                $setup->getIdxName('swissup_navigationpro_item', ['parent_id']),
                ['parent_id']
            )
            ->addIndex(
                $setup->getIdxName('swissup_navigationpro_item', ['menu_id']),
                ['menu_id']
            )
            ->addIndex(
                $setup->getIdxName('swissup_navigationpro_item', ['level']),
                ['level']
            )
            ->addIndex(
                $setup->getIdxName('swissup_navigationpro_item', ['remote_entity_type']),
                ['remote_entity_type']
            )
            ->addIndex(
                $setup->getIdxName('swissup_navigationpro_item', ['remote_entity_id']),
                ['remote_entity_id']
            )
            ->addForeignKey(
                $setup->getFkName(
                    'swissup_navigationpro_item',
                    'parent_id',
                    'swissup_navigationpro_item',
                    'item_id'
                ),
                'parent_id',
                $setup->getTable('swissup_navigationpro_item'),
                'item_id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $setup->getFkName(
                    'swissup_navigationpro_item',
                    'menu_id',
                    'swissup_navigationpro_menu',
                    'menu_id'
                ),
                'menu_id',
                $setup->getTable('swissup_navigationpro_menu'),
                'menu_id',
                Table::ACTION_CASCADE
            )
            ->setComment(
                'NavigationPro Item Table'
            );
        $setup->getConnection()->createTable($table);

        $table = $setup->getConnection()
            ->newTable($setup->getTable('swissup_navigationpro_item_content'))
            ->addColumn(
                'item_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'nullable' => false,
                    'unsigned' => true,
                ],
                'Item ID'
            )
            ->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                [
                    'nullable' => false,
                    'unsigned' => true,
                    'default' => '0'
                ],
                'Store ID'
            )
            ->addColumn(
                'name',
                Table::TYPE_TEXT,
                255,
                [],
                'Item Name'
            )
            ->addColumn(
                'url_path',
                Table::TYPE_TEXT,
                255,
                [],
                'Item URL Path'
            )
            ->addColumn(
                'html',
                Table::TYPE_TEXT,
                null,
                [],
                'Item HTML'
            )
            ->addColumn(
                'css_class',
                Table::TYPE_TEXT,
                null,
                [],
                'CSS Class'
            )
            ->addColumn(
                'dropdown_settings',
                Table::TYPE_TEXT,
                null,
                [],
                'Dropdown Settings'
            )
            ->addIndex(
                $setup->getIdxName('swissup_navigationpro_item_content', ['item_id', 'store_id']),
                ['item_id', 'store_id'],
                [
                    'type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_PRIMARY
                ]
            )
            ->addIndex(
                $setup->getIdxName('swissup_navigationpro_item_content', ['store_id']),
                ['store_id']
            )
            ->addForeignKey(
                $setup->getFkName(
                    'swissup_navigationpro_item_content',
                    'item_id',
                    'swissup_navigationpro_item',
                    'item_id'
                ),
                'item_id',
                $setup->getTable('swissup_navigationpro_item'),
                'item_id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $setup->getFkName(
                    'swissup_navigationpro_item_content',
                    'store_id',
                    'store',
                    'store_id'
                ),
                'store_id',
                $setup->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )
            ->setComment(
                'NavigationPro Item Content Table'
            );
        $setup->getConnection()->createTable($table);
    }

    protected function addItemSettings(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable('swissup_navigationpro_menu'),
            'item_settings',
            [
                'after' => 'dropdown_settings',
                'type' => Table::TYPE_TEXT,
                'length' => null,
                'nullable' => true,
                'default'  => null,
                'comment' => 'Item Settings'
            ]
        );
    }

    protected function addStyles(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable('swissup_navigationpro_menu'),
            'styles',
            [
                'after' => 'css_class',
                'type' => Table::TYPE_TEXT,
                'length' => null,
                'nullable' => true,
                'default'  => null,
                'comment' => 'Menu Styles'
            ]
        );
    }
}

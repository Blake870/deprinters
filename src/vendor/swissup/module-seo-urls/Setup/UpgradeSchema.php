<?php

namespace Swissup\SeoUrls\Setup;

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

        if (version_compare($context->getVersion(), '1.4.0', '<')) {
            $this->createTable($setup);
        }

        if (version_compare($context->getVersion(), '1.5.0', '<')) {
            $this->createTableAdvancedProperties($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param  SchemaSetupInterface $setup
     */
    protected function createTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable('swissup_seourls_attribute_label'))
            ->addColumn(
                'attribute_label_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Attribute Label Id'
            )->addColumn(
                'attribute_id',
                Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => '0'
                ],
                'Attribute Id'
            )->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => '0'
                ],
                'Store Id'
            )->addColumn(
                'value',
                Table::TYPE_TEXT,
                255,
                [
                    'nullable' => true,
                    'default' => null
                ],
                'Value'
            )->addIndex(
                $setup->getIdxName(
                    'swissup_seourls_attribute_label',
                    ['attribute_id', 'store_id']
                ),
                ['attribute_id', 'store_id']
            )->addForeignKey(
                $setup->getFkName(
                    'swissup_seourls_attribute_label',
                    'attribute_id',
                    'eav_attribute',
                    'attribute_id'
                ),
                'attribute_id',
                $setup->getTable('eav_attribute'),
                'attribute_id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    'swissup_seourls_attribute_label',
                    'store_id',
                    'store',
                    'store_id'
                ),
                'store_id',
                $setup->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment(
                'Swissup SEO URLs Attribute Label'
            );
        $setup->getConnection()->createTable($table);

        $table = $setup->getConnection()
            ->newTable($setup->getTable('swissup_seourls_attribute_value'))
            ->addColumn(
                'attribute_value_id',
                Table::TYPE_BIGINT,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Attribute Vaule Id'
            )->addColumn(
                'attribute_id',
                Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => '0'
                ],
                'Attribute Id'
            )->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => '0'
                ],
                'Store Id'
            )->addColumn(
                'original_value',
                Table::TYPE_TEXT,
                255,
                [
                    'nullable' => true,
                    'default' => null
                ],
                'Original Value'
            )
            ->addColumn(
                'url_value',
                Table::TYPE_TEXT,
                255,
                [
                    'nullable' => true,
                    'default' => null
                ],
                'In-URL Value'
            )->addIndex(
                $setup->getIdxName(
                    'swissup_seourls_attribute_value',
                    ['attribute_id', 'store_id']
                ),
                ['attribute_id', 'store_id']
            )->addForeignKey(
                $setup->getFkName(
                    'swissup_seourls_attribute_value',
                    'attribute_id',
                    'eav_attribute',
                    'attribute_id'
                ),
                'attribute_id',
                $setup->getTable('eav_attribute'),
                'attribute_id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    'swissup_seourls_attribute_value',
                    'store_id',
                    'store',
                    'store_id'
                ),
                'store_id',
                $setup->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment(
                'Swissup SEO URLs Attribute Values'
            );
        $setup->getConnection()->createTable($table);

        $table = $setup->getConnection()
            ->newTable($setup->getTable('swissup_seourls_category_label'))
            ->addColumn(
                'category_label_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Category Label Id'
            )->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => '0'
                ],
                'Entity Id'
            )->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => '0'
                ],
                'Store Id'
            )->addColumn(
                'value',
                Table::TYPE_TEXT,
                255,
                [
                    'nullable' => true,
                    'default' => null
                ],
                'Value'
            )->addIndex(
                $setup->getIdxName(
                    'swissup_seourls_category_label',
                    ['entity_id', 'store_id']
                ),
                ['entity_id', 'store_id']
            )->addForeignKey(
                $setup->getFkName(
                    'swissup_seourls_category_label',
                    'entity_id',
                    'catalog_category_entity',
                    'entity_id'
                ),
                'entity_id',
                $setup->getTable('catalog_category_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    'swissup_seourls_category_label',
                    'store_id',
                    'store',
                    'store_id'
                ),
                'store_id',
                $setup->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment(
                'Swissup SEO URLs Category Label'
            );
        $setup->getConnection()->createTable($table);
    }

    public function createTableAdvancedProperties(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable('swissup_seourls_attribute_advanced'))
            ->addColumn(
                'attribute_id',
                Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Attribute Id'
            )->addColumn(
                'is_nofollow',
                Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => '0'
                ],
                'Is nofollow'
            )->addForeignKey(
                $setup->getFkName(
                    'swissup_seourls_attribute_advanced',
                    'attribute_id',
                    'eav_attribute',
                    'attribute_id'
                ),
                'attribute_id',
                $setup->getTable('eav_attribute'),
                'attribute_id',
                Table::ACTION_CASCADE
            )->setComment(
                'Swissup SEO URLs Attribute - advanced properties'
            );
        $setup->getConnection()->createTable($table);
    }
}

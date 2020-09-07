<?php

namespace Swissup\SeoImages\Setup;

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

        if (version_compare($context->getVersion(), '1.0.0', '<')) {
            $this->createTable($setup);
        }

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->createTableParams($setup);
        }

        $setup->endSetup();
    }

    /**
     * Create table
     *
     * @param  SchemaSetupInterface $setup
     */
    protected function createTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable('swissup_seoimages'))
            ->addColumn(
                'file_key',
                Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                    'primary'  => true,
                    'unsigned' => true,
                ],
                'File Key'
            )
            ->addColumn(
                'original_file',
                Table::TYPE_TEXT,
                255,
                [],
                'Original file name'
            )
            ->addColumn(
                'target_file',
                Table::TYPE_TEXT,
                255,
                [],
                'Target file name'
            )
            ;
        $setup->getConnection()->createTable($table);
    }

    /**
     * Create table seoimages_params
     *
     * @param  SchemaSetupInterface $setup
     */
    protected function createTableParams(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable('swissup_seoimages_params'))
            ->addColumn(
                'params_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'primary'  => true,
                    'nullable' => false,
                    'unsigned' => true
                ],
                'Id'
            )
            ->addColumn(
                'params',
                Table::TYPE_TEXT,
                255,
                [],
                'Params string'
            )
            ;
        $setup->getConnection()->createTable($table);
    }
}

<?php
namespace Swissup\SeoHtmlSitemap\Setup;

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
         * Create table 'swissup_seohtmlsitemap_links'
         */

        $table = $installer->getConnection()
            ->newTable($installer->getTable('swissup_seohtmlsitemap_links'))
            ->addColumn(
                'link_id', Table::TYPE_INTEGER, null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Link ID')
            ->addColumn(
                'status', Table::TYPE_SMALLINT, null, ['nullable' => false, 'default' => '1'],
                'Link status')
            ->addColumn('name', Table::TYPE_TEXT, 100, ['nullable' => false], 'Link name')
            ->addColumn('url', Table::TYPE_TEXT, 100, ['nullable' => false], 'Link url')
            ->addColumn('creation_time', Table::TYPE_DATETIME, null, [], 'Link creation time')
            ->addColumn('update_time', Table::TYPE_DATETIME, null, [], 'Link update time')
            ->addIndex(
                $setup->getIdxName(
                    $installer->getTable('swissup_seohtmlsitemap_links'),
                    ['name', 'url'],
                    AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                ['name', 'url'],
                ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
            )
            ->setComment('Swissup SEO HTML Sitemap Links Table');

        $installer->getConnection()->createTable($table);

        /**
         * Create table 'swissup_seohtmlsitemap_store'
         */

        $table = $installer->getConnection()
            ->newTable($installer->getTable('swissup_seohtmlsitemap_store'))
            ->addColumn(
                'link_id', Table::TYPE_INTEGER, null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Link ID')
            ->addColumn(
                'store_id', Table::TYPE_SMALLINT, null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store ID')
            ->addIndex(
                $installer->getIdxName('swissup_seohtmlsitemap_store', ['store_id']),
                ['store_id'])
            ->addForeignKey(
                $installer->getFkName('swissup_seohtmlsitemap_store', 'link_id',
                    'swissup_seohtmlsitemap_links', 'link_id'), 'link_id',
                $installer->getTable('swissup_seohtmlsitemap_links'), 'link_id',
                Table::ACTION_CASCADE)
            ->addForeignKey(
                $installer->getFkName('swissup_seohtmlsitemap_store', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'), 'store_id',
                Table::ACTION_CASCADE)
            ->setComment('Swissup SEO HTML Sitemap To Store Linkage Table');

        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}

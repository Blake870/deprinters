<?php

namespace Swissup\Gdpr\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * Upgrade DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $connection = $setup->getConnection();

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            /**
             * Create table 'swissup_gdpr_client_consent'
             */
            $table = $connection->newTable(
                    $setup->getTable('swissup_gdpr_client_consent')
                )
                ->addColumn(
                    'entity_id',
                    Table::TYPE_BIGINT,
                    null,
                    [
                        'identity'  => true,
                        'unsigned'  => true,
                        'nullable'  => false,
                        'primary'   => true,
                    ],
                    'Entity ID'
                )
                ->addColumn(
                    'client_identity_field',
                    Table::TYPE_TEXT,
                    32,
                    ['nullable' => false],
                    'Client\'s Identity Field'
                )
                ->addColumn(
                    'client_identity',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Client\'s Identity Value'
                )
                ->addColumn(
                    'visitor_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true, 'default' => '0'],
                    'Visitor ID'
                )
                ->addColumn(
                    'customer_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => true, 'unsigned' => true],
                    'Customer ID'
                )
                ->addColumn(
                    'form_id',
                    Table::TYPE_TEXT,
                    64,
                    ['nullable' => false],
                    'Form ID'
                )
                ->addColumn(
                    'consent_id',
                    Table::TYPE_TEXT,
                    64,
                    ['nullable' => false],
                    'Consent ID'
                )
                ->addColumn(
                    'consent_title',
                    Table::TYPE_TEXT,
                    512,
                    ['nullable' => false],
                    'Consent title'
                )
                ->addColumn(
                    'updated_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                    'Update Time'
                )
                ->addForeignKey(
                    $setup->getFkName(
                        'swissup_gdpr_client_consent',
                        'customer_id',
                        'customer_entity',
                        'entity_id'
                    ),
                    'customer_id',
                    $setup->getTable('customer_entity'),
                    'entity_id',
                    Table::ACTION_CASCADE
                )
                ->setComment(
                    'List of approved consents'
                );
            $connection->createTable($table);
        }

        if (version_compare($context->getVersion(), '1.2.0', '<')) {
            /**
             * Create table 'swissup_gdpr_client_request'
             */
            $table = $connection->newTable(
                    $setup->getTable('swissup_gdpr_client_request')
                )
                ->addColumn(
                    'entity_id',
                    Table::TYPE_BIGINT,
                    null,
                    [
                        'identity'  => true,
                        'unsigned'  => true,
                        'nullable'  => false,
                        'primary'   => true,
                    ],
                    'Entity ID'
                )
                ->addColumn(
                    'client_identity_field',
                    Table::TYPE_TEXT,
                    32,
                    ['nullable' => false],
                    'Client\'s Identity Field'
                )
                ->addColumn(
                    'client_identity',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Client\'s Identity Value'
                )
                ->addColumn(
                    'customer_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => true, 'unsigned' => true],
                    'Customer ID'
                )
                ->addColumn(
                    'type',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false],
                    'Request Type'
                )
                ->addColumn(
                    'confirmation_token',
                    Table::TYPE_TEXT,
                    64,
                    ['nullable' => false],
                    'Confirmation Token'
                )
                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                    'Created At'
                )
                ->addColumn(
                    'confirmed_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => true, 'default' => null],
                    'Confirmed At'
                )
                ->addColumn(
                    'executed_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => true, 'default' => null],
                    'Executed At'
                )
                ->addColumn(
                    'status',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'default' => 0],
                    'Status'
                )
                ->addForeignKey(
                    $setup->getFkName(
                        'swissup_gdpr_client_request',
                        'customer_id',
                        'customer_entity',
                        'entity_id'
                    ),
                    'customer_id',
                    $setup->getTable('customer_entity'),
                    'entity_id',
                    Table::ACTION_SET_NULL
                )
                ->setComment(
                    'List of client requests'
                );
            $connection->createTable($table);
        }

        if (version_compare($context->getVersion(), '1.2.1', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('swissup_gdpr_client_request'),
                'report',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Report Messages'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.2.2', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('swissup_gdpr_client_consent'),
                'website_id',
                [
                    'type' => Table::TYPE_SMALLINT,
                    'unsigned' => true,
                    'default' => 0,
                    'comment' => 'Website ID',
                    'after' => 'entity_id'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('swissup_gdpr_client_request'),
                'website_id',
                [
                    'type' => Table::TYPE_SMALLINT,
                    'unsigned' => true,
                    'default' => 0,
                    'comment' => 'Website ID',
                    'after' => 'entity_id'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.2.3', '<')) {
            $setup->getConnection()->addIndex(
                $setup->getTable('swissup_gdpr_client_consent'),
                $setup->getIdxName(
                    $setup->getTable('swissup_gdpr_client_consent'),
                    ['website_id']
                ),
                ['website_id']
            );
            $setup->getConnection()->addIndex(
                $setup->getTable('swissup_gdpr_client_consent'),
                $setup->getIdxName(
                    $setup->getTable('swissup_gdpr_client_consent'),
                    ['client_identity_field']
                ),
                ['client_identity_field']
            );
            $setup->getConnection()->addIndex(
                $setup->getTable('swissup_gdpr_client_consent'),
                $setup->getIdxName(
                    $setup->getTable('swissup_gdpr_client_consent'),
                    ['client_identity']
                ),
                ['client_identity']
            );
            $setup->getConnection()->addIndex(
                $setup->getTable('swissup_gdpr_client_consent'),
                $setup->getIdxName(
                    $setup->getTable('swissup_gdpr_client_consent'),
                    ['visitor_id']
                ),
                ['visitor_id']
            );
            $setup->getConnection()->addIndex(
                $setup->getTable('swissup_gdpr_client_consent'),
                $setup->getIdxName(
                    $setup->getTable('swissup_gdpr_client_consent'),
                    ['updated_at']
                ),
                ['updated_at']
            );

            $setup->getConnection()->addIndex(
                $setup->getTable('swissup_gdpr_client_request'),
                $setup->getIdxName(
                    $setup->getTable('swissup_gdpr_client_request'),
                    ['website_id']
                ),
                ['website_id']
            );
            $setup->getConnection()->addIndex(
                $setup->getTable('swissup_gdpr_client_request'),
                $setup->getIdxName(
                    $setup->getTable('swissup_gdpr_client_request'),
                    ['client_identity_field']
                ),
                ['client_identity_field']
            );
            $setup->getConnection()->addIndex(
                $setup->getTable('swissup_gdpr_client_request'),
                $setup->getIdxName(
                    $setup->getTable('swissup_gdpr_client_request'),
                    ['client_identity']
                ),
                ['client_identity']
            );
            $setup->getConnection()->addIndex(
                $setup->getTable('swissup_gdpr_client_request'),
                $setup->getIdxName(
                    $setup->getTable('swissup_gdpr_client_request'),
                    ['type']
                ),
                ['type']
            );
            $setup->getConnection()->addIndex(
                $setup->getTable('swissup_gdpr_client_request'),
                $setup->getIdxName(
                    $setup->getTable('swissup_gdpr_client_request'),
                    ['confirmation_token']
                ),
                ['confirmation_token']
            );
            $setup->getConnection()->addIndex(
                $setup->getTable('swissup_gdpr_client_request'),
                $setup->getIdxName(
                    $setup->getTable('swissup_gdpr_client_request'),
                    ['created_at']
                ),
                ['created_at']
            );
            $setup->getConnection()->addIndex(
                $setup->getTable('swissup_gdpr_client_request'),
                $setup->getIdxName(
                    $setup->getTable('swissup_gdpr_client_request'),
                    ['executed_at']
                ),
                ['executed_at']
            );
            $setup->getConnection()->addIndex(
                $setup->getTable('swissup_gdpr_client_request'),
                $setup->getIdxName(
                    $setup->getTable('swissup_gdpr_client_request'),
                    ['status']
                ),
                ['status']
            );
        }

        $setup->endSetup();
    }
}

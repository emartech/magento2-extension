<?php

namespace Emartech\Emarsys\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

/**
 * Class InstallSchema
 * @package Emartech\Emarsys\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     *
     * @return void
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->createEmarsysEventsTable($setup);

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     *
     * @throws \Zend_Db_Exception
     */
    private function createEmarsysEventsTable(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable('emarsys_events_data');
        if ($setup->getConnection()->isTableExists($tableName) != true) {
            $table = $setup->getConnection()->newTable(
                $setup->getTable('emarsys_events_data'))
                ->addColumn(
                    'event_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Event Id'
                )
                ->addColumn(
                    'website_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['default' => null, 'nullable' => true],
                    'Website ID'
                )
                ->addColumn(
                    'store_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['default' => null, 'nullable' => true],
                    'Store ID'
                )->addColumn(
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false],
                    'Entity ID'
                )
                ->addColumn(
                    'event_type',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['default' => null, 'nullable' => false],
                    'Event Type'
                )
                ->addColumn(
                    'event_data',
                    \Magento\Framework\DB\Ddl\Table::TYPE_BLOB,
                    null,
                    ['default' => null, 'nullable' => false],
                    'Event Data'
                )
                ->addColumn(
                    'created_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    ['default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT, 'nullable' => false],
                    'Timestamp'
                )
                ->addIndex(
                    $setup->getIdxName(
                        $setup->getTable('emarsys_events_data'),
                        ['event_type'],
                        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
                    ),
                    ['event_type'],
                    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX]
                )
                ->addIndex(
                    $setup->getIdxName(
                        $setup->getTable('emarsys_events_data'),
                        ['created_at'],
                        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
                    ),
                    ['created_at'],
                    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX]
                );
            $setup->getConnection()->createTable($table);
        }
    }
}

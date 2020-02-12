<?php

namespace Emartech\Emarsys\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.5.3') < 0) {
            $tableName = $setup->getTable('emarsys_events_data');

            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $setup->getConnection()->modifyColumn($tableName, 'event_data', 'mediumblob');
            }
        }
        if(version_compare($context->getVersion(), '1.9.1', '<')) {
            $this->createEmarsysProductDeltaTable($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function createEmarsysProductDeltaTable(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable('emarsys_product_delta');
        if (!$setup->tableExists($tableName)) {
            $table = $setup->getConnection()->newTable(
                $tableName
            )->addColumn(
                'product_delta_id',
                Table::TYPE_BIGINT,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary'  => true,
                ],
                'Product Delta Id'
            )->addColumn(
                'sku',
                Table::TYPE_TEXT,
                64,
                [
                    'default'  => null,
                    'nullable' => true,
                ],
                'Product SKU'
            )->addColumn(
                'entity_id',
                Table::TYPE_BIGINT,
                64,
                [
                    'default'  => null,
                    'nullable' => true,
                ],
                'Product Entity ID'
            )->addColumn(
                'row_id',
                Table::TYPE_BIGINT,
                64,
                [
                    'default'  => null,
                    'nullable' => true,
                ],
                'Product Row ID'
            );

            $setup->getConnection()->createTable($table);
        }
    }
}

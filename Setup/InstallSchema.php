<?php

namespace Emartech\Emarsys\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class InstallSchema implements InstallSchemaInterface
{
  public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
  {
    $setup->startSetup();

    $tableName = $setup->getTable('emarsys_settings');
    if ($setup->getConnection()->isTableExists($tableName) != true) {
      $table = $setup->getConnection()->newTable(
        $setup->getTable('emarsys_settings'))
        ->addColumn(
          'setting_id',
          \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
          null,
          ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
          'Id'
        )
        ->addColumn(
          'setting',
          \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
          255,
          ['default' => null, 'nullable' => false],
          'Setting'
        )
        ->addColumn(
          'value',
          \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
          null,
          ['default' => null, 'nullable' => false],
          'Value'
        )
        ->addIndex(
          $setup->getIdxName(
            $setup->getTable('emarsys_settings'),
            ['setting'],
            \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
          ),
          ['setting'],
          ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        );
      $setup->getConnection()->createTable($table);
    }

    $setup->endSetup();
  }
}
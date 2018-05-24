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

    $table = $setup->getConnection()->newTable(
      $setup->getTable('emarsys_settings'))
      ->addColumn(
        'id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
        'Id'
      )
      ->addColumn(
        'key',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        null,
        ['default' => null, 'nullable' => false],
        'Key'
      )
      ->addColumn(
        'value',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        null,
        ['default' => null, 'nullable' => false],
        'Value'
      );
    $setup->getConnection()->createTable($table);

    $setup->endSetup();
  }
}
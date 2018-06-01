<?php


namespace Emartech\Emarsys\Setup;

use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Emartech\Emarsys\Helper\Integration;

class Uninstall implements UninstallInterface
{
  /**
   * @var Integration
   */
  private $integration;

  /**
   * InstallData constructor.
   * @param Integration $integration
   */
  public function __construct(Integration $integration)
  {
    $this->integration = $integration;
  }

  public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
  {
    $setup->startSetup();

    // Do not re-create integration for development purposes
    // $this->integration->delete();

    $tableName = $setup->getTable('emarsys_settings');
    if ($setup->getConnection()->isTableExists($tableName) === true) {
      $setup->getConnection()->dropTable($tableName);
    }

    $setup->endSetup();
  }
}
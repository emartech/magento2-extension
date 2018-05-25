<?php


namespace Emartech\Emarsys\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Emartech\Emarsys\Helper\Integration;

class InstallData implements InstallDataInterface
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

  /**
   * @param ModuleDataSetupInterface $setup
   * @param ModuleContextInterface $context
   * @throws \Magento\Framework\Oauth\Exception
   */
  public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
  {
    $setup->startSetup();

    $this->integration->create();
    $this->integration->saveConnectTokenToConfig();

    $tableName = $setup->getTable('emarsys_settings');
    if ($setup->getConnection()->isTableExists($tableName) === true) {
      $data = [
        [
          'setting' => 'collectCustomerEvents',
          'value' => 'disabled'
        ],
        [
          'setting' => 'collectSalesEvents',
          'value' => 'disabled'
        ],
        [
          'setting' => 'collectProductEvents',
          'value' => 'disabled'
        ]
      ];

      foreach ($data as $item) {
        $setup->getConnection()->insert($tableName, $item);
      }
    }

    $setup->endSetup();
  }
}
<?php


namespace Emartech\Emarsys\Model\Api;

use Emartech\Emarsys\Api\SettingsApiInterface;
use Emartech\Emarsys\Model\ResourceModel\Settings;
use Emartech\Emarsys\Model\SettingsFactory;

class SettingsApi implements SettingsApiInterface
{
  /**  @var SettingsFactory */
  protected $settingsFactory;
  /** @var Settings */
  private $settingsResource;

  public function __construct(SettingsFactory $settingsFactory, Settings $resourceModel)
  {
    $this->settingsFactory = $settingsFactory;
    $this->settingsResource = $resourceModel;
  }

  public function set($collectCustomerEvents = null, $collectSalesEvents = null, $collectProductEvents = null)
  {
    if ($collectCustomerEvents !== null) {
      $this->saveSetting('collectCustomerEvents', $collectCustomerEvents);
    }

    if ($collectSalesEvents !== null) {
      $this->saveSetting('collectSalesEvents', $collectSalesEvents);
    }

    if ($collectProductEvents !== null) {
      $this->saveSetting('collectProductEvents', $collectProductEvents);
    }

    return 'OK';
  }

  private function saveSetting($settingName, $settingValue)
  {
    $settingModel = $this->settingsFactory->create();
    $this->settingsResource->load($settingModel, $settingName, 'setting');
    $settingModel->setData('setting', $settingName);
    $settingModel->setData('value', $settingValue);
    $this->settingsResource->save($settingModel);
  }
}
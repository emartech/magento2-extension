<?php


namespace Emartech\Emarsys\Helper;


use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Emartech\Emarsys\Model\SettingsFactory;

class Data extends AbstractHelper
{
  const ENABLED = 'enabled';

  const EXTERNAL_EVENTS = 'collectExternalEvents';
  const SALES_EVENTS = 'collectSalesEvents';
  const CUSTOMER_EVENTS = 'collectCustomerEvents';

  /**
   * @var SettingsFactory
   */
  private $settingsFactory;

  public function __construct(
    Context $context,
    SettingsFactory $settingsFactory
  ) {
    parent::__construct($context);
    $this->settingsFactory = $settingsFactory;
  }

  public function isEnabled($setting)
  {
    $settingsResource = $this->settingsFactory->create();
    $setting = $settingsResource->load($setting, 'setting');

    return $setting->getValue() === self::ENABLED;
  }
}
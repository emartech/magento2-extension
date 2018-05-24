<?php


namespace Emartech\Emarsys\Model;

use Emartech\Emarsys\Api\SettingsInterface;

class EmarsysSettingsApi implements SettingsInterface
{
  /**
   * @var EmarsysSettingsFactory
   */
  protected $settingsFactory;

  public function __construct(EmarsysSettingsFactory $settingsFactory)
  {
    $this->settingsFactory = $settingsFactory;
  }
  
  public function set($key, $value)
  {
    var_dump($key);
    var_dump($value);
    return 'false';
  }
}
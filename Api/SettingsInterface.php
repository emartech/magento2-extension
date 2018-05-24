<?php


namespace Emartech\Emarsys\Api;


interface SettingsInterface
{
  /**
   * @param string $key
   * @param string $value
   * @return mixed
   */
  public function set($key, $value);
}
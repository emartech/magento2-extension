<?php


namespace Emartech\Emarsys\Api;


interface SettingsApiInterface
{
  /**
   * @param string $collectCustomerEvents
   * @param string $collectSalesEvents
   * @param string $collectProductEvents
   * @param string $merchantId
   * @return mixed
   */
  public function set($collectCustomerEvents = null, $collectSalesEvents = null, $collectProductEvents = null, $merchantId = null);
}
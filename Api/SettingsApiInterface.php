<?php


namespace Emartech\Emarsys\Api;


interface SettingsApiInterface
{
  /**
   * @param string $collectCustomerEvents
   * @param string $collectSalesEvents
   * @param string $collectProductEvents
   * @param string $merchantId
   * @param string $injectSnippet
   * @param string $webTrackingSnippetUrl
   * @return mixed
   */
  public function set($collectCustomerEvents = null,
    $collectSalesEvents = null,
    $collectProductEvents = null,
    $merchantId = null,
    $injectSnippet = null,
    $webTrackingSnippetUrl = null
  );
}
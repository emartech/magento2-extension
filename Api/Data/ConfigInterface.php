<?php

namespace Emartech\Emarsys\Api\Data;

use Emartech\Emarsys\Model\Data\Config;

interface ConfigInterface
{
  const CONFIG_ENABLED = 'enabled';
  const CONFIG_DISBALED = 'disabled';
  const CUSTOMER_EVENTS = 'collect_customer_events';
  const SALES_EVENTS = 'collect_sales_events';
  const MARKETING_EVENTS = 'collect_marketing_events';
  const INJECT_WEBEXTEND_SNIPPETS = 'inject_webextend_snippets';
  const MERCHANT_ID = 'merchant_id';
  const SNIPPET_URL = 'web_tracking_snippet_url';


  /**
   * @return array
   */
  public function getData();

  /**
   * @return string
   */
  public function getCollectCustomerEvents();

  /**
   * @param string $collectCustomerEvents
   * @return Config
   */
  public function setCollectCustomerEvents($collectCustomerEvents);

  /**
   * @return string
   */
  public function getCollectSalesEvents();

  /**
   * @param string $collectSalesEvents
   * @return Config
   */
  public function setCollectSalesEvents($collectSalesEvents);

  /**
   * @return string
   */
  public function getCollectMarketingEvents();

  /**
   * @param string $collectMarketingEvents
   * @return Config
   */
  public function setCollectMarketingEvents($collectMarketingEvents);

  /**
   * @return string
   */
  public function getMerchantId();

  /**
   * @param string $merchantId
   * @return Config
   */
  public function setMerchantId($merchantId);

  /**
   * @return string
   */
  public function getInjectSnippet();

  /**
   * @param string $injectSnippet
   * @return Config
   */
  public function setInjectSnippet($injectSnippet);

  /**
   * @return string
   */
  public function getWebTrackingSnippetUrl();

  /**
   * @param string $webTrackingSnippetUrl
   * @return Config
   */
  public function setWebTrackingSnippetUrl($webTrackingSnippetUrl);
}

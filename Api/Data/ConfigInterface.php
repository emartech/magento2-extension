<?php

namespace Emartech\Emarsys\Api\Data;

use Emartech\Emarsys\Model\Data\Config;

/**
 * Interface ConfigInterface
 * @package Emartech\Emarsys\Api\Data
 */
interface ConfigInterface
{
    const CONFIG_ENABLED = 'enabled';
    const CONFIG_DISABLED = 'disabled';
    const CONFIG_EMPTY = null;
    const CUSTOMER_EVENTS = 'collect_customer_events';
    const SALES_EVENTS = 'collect_sales_events';
    const MARKETING_EVENTS = 'collect_marketing_events';
    const INJECT_WEBEXTEND_SNIPPETS = 'inject_webextend_snippets';
    const MERCHANT_ID = 'merchant_id';
    const SNIPPET_URL = 'web_tracking_snippet_url';
    const STORE_SETTINGS = 'store_settings';

    const SCOPE_TYPE_DEFAULT = 'websites';
    const XML_PATH_EMARSYS_PRE_TAG = 'emartech/emarsys/config/';

    /**
     * @return string[]
     */
    public function getData();

    /**
     * @return string
     */
    public function getCollectCustomerEvents();

    /**
     * @param string $collectCustomerEvents
     *
     * @return Config
     */
    public function setCollectCustomerEvents($collectCustomerEvents);

    /**
     * @return string
     */
    public function getCollectSalesEvents();

    /**
     * @param string $collectSalesEvents
     *
     * @return Config
     */
    public function setCollectSalesEvents($collectSalesEvents);

    /**
     * @return string
     */
    public function getCollectMarketingEvents();

    /**
     * @param string $collectMarketingEvents
     *
     * @return Config
     */
    public function setCollectMarketingEvents($collectMarketingEvents);

    /**
     * @return string
     */
    public function getMerchantId();

    /**
     * @param string $merchantId
     *
     * @return Config
     */
    public function setMerchantId($merchantId);

    /**
     * @return string
     */
    public function getInjectSnippet();

    /**
     * @param string $injectSnippet
     *
     * @return Config
     */
    public function setInjectSnippet($injectSnippet);

    /**
     * @return string
     */
    public function getWebTrackingSnippetUrl();

    /**
     * @param string $webTrackingSnippetUrl
     *
     * @return Config
     */
    public function setWebTrackingSnippetUrl($webTrackingSnippetUrl);

    /**
     * @param string $xmlPostPath
     * @param string $value
     * @param int    $scopeId
     * @param string $scope
     *
     * @return void
     */
    public function setConfigValue($xmlPostPath, $value, $scopeId, $scope = self::SCOPE_TYPE_DEFAULT);

    /**
     * @return void
     */
    public function cleanScope();

    /**
     * @return \Emartech\Emarsys\Api\Data\StoreConfigInterface[]
     */
    public function getStoreSettings();

    /**
     * @param \Emartech\Emarsys\Api\Data\StoreConfigInterface[] $storeSettings
     *
     * @return $this
     */
    public function setStoreSettings($storeSettings);

    /**
     * @param string   $key
     * @param null|int $websiteId
     *
     * @return string|null
     */
    public function getConfigValue($key, $websiteId = null);

    /**
     * @param string   $key
     * @param null|int $websiteId
     *
     * @return bool
     */
    public function isEnabledForWebsite($key, $websiteId = 0);

    /**
     * @param string   $key
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isEnabledForStore($key, $storeId = null);
}

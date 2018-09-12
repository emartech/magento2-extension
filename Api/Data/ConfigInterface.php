<?php

namespace Emartech\Emarsys\Api\Data;

/**
 * Interface ConfigInterface
 * @package Emartech\Emarsys\Api\Data
 */
interface ConfigInterface
{
    const CONFIG_ENABLED            = 'enabled';
    const CONFIG_DISABLED           = 'disabled';
    const CONFIG_EMPTY              = null;
    const CUSTOMER_EVENTS           = 'collect_customer_events';
    const SALES_EVENTS              = 'collect_sales_events';
    const MARKETING_EVENTS          = 'collect_marketing_events';
    const INJECT_WEBEXTEND_SNIPPETS = 'inject_webextend_snippets';
    const MERCHANT_ID               = 'merchant_id';
    const SNIPPET_URL               = 'web_tracking_snippet_url';
    const STORE_SETTINGS            = 'store_settings';

    const SCOPE_TYPE_DEFAULT       = 'websites';
    const XML_PATH_EMARSYS_PRE_TAG = 'emartech/emarsys/config/';

    /**
     * @param string     $key
     * @param string|int $index
     *
     * @return mixed
     */
    public function getData($key = '', $index = null);

    /**
     * @return string
     */
    public function getCollectCustomerEvents(): string;

    /**
     * @param string $collectCustomerEvents
     *
     * @return $this
     */
    public function setCollectCustomerEvents($collectCustomerEvents): ConfigInterface;

    /**
     * @return string
     */
    public function getCollectSalesEvents(): string;

    /**
     * @param string $collectSalesEvents
     *
     * @return $this
     */
    public function setCollectSalesEvents($collectSalesEvents): ConfigInterface;

    /**
     * @return string
     */
    public function getCollectMarketingEvents(): string;

    /**
     * @param string $collectMarketingEvents
     *
     * @return $this
     */
    public function setCollectMarketingEvents($collectMarketingEvents): ConfigInterface;

    /**
     * @return string
     */
    public function getMerchantId(): string;

    /**
     * @param string $merchantId
     *
     * @return $this
     */
    public function setMerchantId($merchantId): ConfigInterface;

    /**
     * @return string
     */
    public function getInjectSnippet(): string;

    /**
     * @param string $injectSnippet
     *
     * @return $this
     */
    public function setInjectSnippet($injectSnippet): ConfigInterface;

    /**
     * @return string
     */
    public function getWebTrackingSnippetUrl(): string;

    /**
     * @param string $webTrackingSnippetUrl
     *
     * @return $this
     */
    public function setWebTrackingSnippetUrl($webTrackingSnippetUrl): ConfigInterface;

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
    public function getStoreSettings(): array;

    /**
     * @param \Emartech\Emarsys\Api\Data\StoreConfigInterface[] $storeSettings
     *
     * @return $this
     */
    public function setStoreSettings($storeSettings): ConfigInterface;

    /**
     * @param string   $key
     * @param null|int $websiteId
     *
     * @return string
     */
    public function getConfigValue($key, $websiteId = null): ?string;

    /**
     * @param string   $key
     * @param null|int $websiteId
     *
     * @return bool
     */
    public function isEnabledForWebsite($key, $websiteId = null): bool;

    /**
     * @param string   $key
     * @param null|int $storeId
     *
     * @return bool
     */
    public function isEnabledForStore($key, $storeId = null): bool;
}

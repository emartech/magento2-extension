<?php

namespace Emartech\Emarsys\Api\Data;

interface ConfigInterface
{
    public const CONFIG_ENABLED            = 'enabled';
    public const CONFIG_DISABLED           = 'disabled';
    public const CONFIG_EMPTY              = null;
    public const CUSTOMER_EVENTS           = 'collect_customer_events';
    public const SALES_EVENTS              = 'collect_sales_events';
    public const MARKETING_EVENTS          = 'collect_marketing_events';
    public const INJECT_WEBEXTEND_SNIPPETS = 'inject_webextend_snippets';
    public const MERCHANT_ID               = 'merchant_id';
    public const SNIPPET_URL               = 'web_tracking_snippet_url';
    public const STORE_SETTINGS            = 'store_settings';
    public const MAGENTO_SEND_EMAIL        = 'magento_send_email';
    public const SCOPE_TYPE_DEFAULT        = 'websites';
    public const XML_PATH_EMARSYS_PRE_TAG  = 'emartech/emarsys/config/';
    public const ATTRIBUTE_CONFIG_POST_TAG = '_attributes';

    /**
     * GetData
     *
     * @param string     $key
     * @param string|int $index
     *
     * @return mixed
     */
    public function getData($key = '', $index = null);

    /**
     * GetCollectCustomerEvents
     *
     * @return string
     */
    public function getCollectCustomerEvents(): string;

    /**
     * SetCollectCustomerEvents
     *
     * @param string $collectCustomerEvents
     *
     * @return \Emartech\Emarsys\Api\Data\ConfigInterface
     */
    public function setCollectCustomerEvents(string $collectCustomerEvents): ConfigInterface;

    /**
     * GetCollectSalesEvents
     *
     * @return string
     */
    public function getCollectSalesEvents(): string;

    /**
     * SetCollectSalesEvents
     *
     * @param string $collectSalesEvents
     *
     * @return \Emartech\Emarsys\Api\Data\ConfigInterface
     */
    public function setCollectSalesEvents(string $collectSalesEvents): ConfigInterface;

    /**
     * GetCollectMarketingEvents
     *
     * @return string
     */
    public function getCollectMarketingEvents(): string;

    /**
     * SetCollectMarketingEvents
     *
     * @param string $collectMarketingEvents
     *
     * @return \Emartech\Emarsys\Api\Data\ConfigInterface
     */
    public function setCollectMarketingEvents(string $collectMarketingEvents): ConfigInterface;

    /**
     * GetMerchantId
     *
     * @return string
     */
    public function getMerchantId(): string;

    /**
     * SetMerchantId
     *
     * @param string $merchantId
     *
     * @return \Emartech\Emarsys\Api\Data\ConfigInterface
     */
    public function setMerchantId(string $merchantId): ConfigInterface;

    /**
     * GetInjectSnippet
     *
     * @return string
     */
    public function getInjectSnippet(): string;

    /**
     * SetInjectSnippet
     *
     * @param string $injectSnippet
     *
     * @return \Emartech\Emarsys\Api\Data\ConfigInterface
     */
    public function setInjectSnippet(string $injectSnippet): ConfigInterface;

    /**
     * GetWebTrackingSnippetUrl
     *
     * @return string
     */
    public function getWebTrackingSnippetUrl(): string;

    /**
     * SetWebTrackingSnippetUrl
     *
     * @param string $webTrackingSnippetUrl
     *
     * @return \Emartech\Emarsys\Api\Data\ConfigInterface
     */
    public function setWebTrackingSnippetUrl(string $webTrackingSnippetUrl): ConfigInterface;

    /**
     * GetMagentoSendEmail
     *
     * @return string
     */
    public function getMagentoSendEmail(): string;

    /**
     * SetMagentoSendEmail
     *
     * @param string $magentoSendEmail
     *
     * @return \Emartech\Emarsys\Api\Data\ConfigInterface
     */
    public function setMagentoSendEmail(string $magentoSendEmail): ConfigInterface;

    /**
     * SetConfigValue
     *
     * @param string          $xmlPostPath
     * @param string|string[] $value
     * @param int             $scopeId
     * @param string          $scope
     *
     * @return bool
     */
    public function setConfigValue(
        string $xmlPostPath,
        $value,
        int $scopeId,
        string $scope = self::SCOPE_TYPE_DEFAULT
    ): bool;

    /**
     * CleanScope
     *
     * @return void
     */
    public function cleanScope(): void;

    /**
     * GetStoreSettings
     *
     * @return \Emartech\Emarsys\Api\Data\StoreConfigInterface[]
     */
    public function getStoreSettings(): array;

    /**
     * SetStoreSettings
     *
     * @param \Emartech\Emarsys\Api\Data\StoreConfigInterface[] $storeSettings
     *
     * @return \Emartech\Emarsys\Api\Data\ConfigInterface
     */
    public function setStoreSettings(array $storeSettings): ConfigInterface;

    /**
     * GetConfigValue
     *
     * @param string   $key
     * @param null|int $websiteId
     *
     * @return string|string[]
     */
    public function getConfigValue(string $key, ?int $websiteId = null);

    /**
     * IsEnabledForWebsite
     *
     * @param string   $key
     * @param null|int $websiteId
     *
     * @return bool
     */
    public function isEnabledForWebsite(string $key, ?int $websiteId = null): bool;

    /**
     * IsEnabledForStore
     *
     * @param string   $key
     * @param null|int $storeId
     *
     * @return bool
     */
    public function isEnabledForStore(string $key, ?int $storeId = null): bool;

    /**
     * GetAvailableWebsites
     *
     * @return \Magento\Store\Api\Data\WebsiteInterface[]
     */
    public function getAvailableWebsites(): array;
}

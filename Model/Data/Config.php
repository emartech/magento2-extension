<?php

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\ConfigInterface;
use Emartech\Emarsys\Api\Data\StoreConfigInterface;
use Emartech\Emarsys\Helper\Json as JsonSerializer;
use Exception;
use Magento\Framework\App\Config as ScopeConfig;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\DataObject;
use Magento\Store\Model\StoreManagerInterface;

class Config extends DataObject implements ConfigInterface
{
    /**
     * @var WriterInterface
     */
    private $configWriter;

    /**
     * @var ScopeConfig
     */
    private $scopeConfig;

    /**
     * @var JsonSerializer
     */
    private $jsonSerializer;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Config constructor.
     *
     * @param WriterInterface       $configWriter
     * @param ScopeConfig           $scopeConfig
     * @param JsonSerializer        $jsonSerializer
     * @param StoreManagerInterface $storeManager
     * @param array                 $data
     */
    public function __construct(
        WriterInterface $configWriter,
        ScopeConfig $scopeConfig,
        JsonSerializer $jsonSerializer,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        parent::__construct($data);

        $this->configWriter = $configWriter;
        $this->scopeConfig = $scopeConfig;
        $this->jsonSerializer = $jsonSerializer;
        $this->storeManager = $storeManager;
    }

    /**
     * GetCollectCustomerEvents
     *
     * @return string
     */
    public function getCollectCustomerEvents(): string
    {
        return (string) $this->getData(self::CUSTOMER_EVENTS);
    }

    /**
     * SetCollectCustomerEvents
     *
     * @param string $collectCustomerEvents
     *
     * @return ConfigInterface
     */
    public function setCollectCustomerEvents(string $collectCustomerEvents): ConfigInterface
    {
        $this->setData(self::CUSTOMER_EVENTS, $collectCustomerEvents);

        return $this;
    }

    /**
     * GetCollectSalesEvents
     *
     * @return string
     */
    public function getCollectSalesEvents(): string
    {
        return (string) $this->getData(self::CUSTOMER_EVENTS);
    }

    /**
     * SetCollectSalesEvents
     *
     * @param string $collectSalesEvents
     *
     * @return ConfigInterface
     */
    public function setCollectSalesEvents(string $collectSalesEvents): ConfigInterface
    {
        $this->setData(self::SALES_EVENTS, $collectSalesEvents);

        return $this;
    }

    /**
     * GetCollectMarketingEvents
     *
     * @return string
     */
    public function getCollectMarketingEvents(): string
    {
        return (string) $this->getData(self::MARKETING_EVENTS);
    }

    /**
     * SetCollectMarketingEvents
     *
     * @param string $collectMarketingEvents
     *
     * @return ConfigInterface
     */
    public function setCollectMarketingEvents(string $collectMarketingEvents): ConfigInterface
    {
        $this->setData(self::MARKETING_EVENTS, $collectMarketingEvents);

        return $this;
    }

    /**
     * GetMerchantId
     *
     * @return string
     */
    public function getMerchantId(): string
    {
        return (string) $this->getData(self::MERCHANT_ID);
    }

    /**
     * SetMerchantId
     *
     * @param string $merchantId
     *
     * @return ConfigInterface
     */
    public function setMerchantId(string $merchantId): ConfigInterface
    {
        $this->setData(self::MERCHANT_ID, $merchantId);

        return $this;
    }

    /**
     * GetInjectSnippet
     *
     * @return string
     */
    public function getInjectSnippet(): string
    {
        return (string) $this->getData(self::INJECT_WEBEXTEND_SNIPPETS);
    }

    /**
     * SetInjectSnippet
     *
     * @param string $injectSnippet
     *
     * @return ConfigInterface
     */
    public function setInjectSnippet(string $injectSnippet): ConfigInterface
    {
        $this->setData(self::INJECT_WEBEXTEND_SNIPPETS, $injectSnippet);

        return $this;
    }

    /**
     * GetWebTrackingSnippetUrl
     *
     * @return string
     */
    public function getWebTrackingSnippetUrl(): string
    {
        return (string) $this->getData(self::SNIPPET_URL);
    }

    /**
     * SetWebTrackingSnippetUrl
     *
     * @param string $webTrackingSnippetUrl
     *
     * @return ConfigInterface
     */
    public function setWebTrackingSnippetUrl(string $webTrackingSnippetUrl): ConfigInterface
    {
        $this->setData(self::SNIPPET_URL, $webTrackingSnippetUrl);

        return $this;
    }

    /**
     * GetMagentoSendEmail
     *
     * @return string
     */
    public function getMagentoSendEmail(): string
    {
        return (string) $this->getData(self::MAGENTO_SEND_EMAIL);
    }

    /**
     * SetMagentoSendEmail
     *
     * @param string $magentoSendEmail
     *
     * @return ConfigInterface
     */
    public function setMagentoSendEmail(string $magentoSendEmail): ConfigInterface
    {
        $this->setData(self::MAGENTO_SEND_EMAIL, $magentoSendEmail);

        return $this;
    }

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
        string $scope = ConfigInterface::SCOPE_TYPE_DEFAULT
    ): bool {
        $xmlPath = self::XML_PATH_EMARSYS_PRE_TAG . trim($xmlPostPath, '/');

        if (is_array($value)) {
            $value = array_map(function ($item) {
                if ($item instanceof DataObject) {
                    $item = $item->toArray();
                }

                return $item;
            }, $value);
        }

        if (!is_string($value) && $value !== null) {
            $value = $this->jsonSerializer->serialize($value);
        }

        $oldConfigValue = $this->scopeConfig->getValue($xmlPath, $scope, $scopeId);

        if ($value == $oldConfigValue) {
            return false;
        }

        $this->configWriter->save($xmlPath, $value, $scope, $scopeId);

        return true;
    }

    /**
     * GetConfigValue
     *
     * @param string   $key
     * @param null|int $websiteId
     *
     * @return string|string[]
     */
    public function getConfigValue(string $key, ?int $websiteId = null)
    {
        if (null === $websiteId) {
            try {
                $websiteId = $this->storeManager->getWebsite()->getId();
            } catch (Exception $e) {
                $websiteId = 0;
            }
        }

        $value = $this->scopeConfig
                     ->getValue(self::XML_PATH_EMARSYS_PRE_TAG . $key, 'websites', $websiteId) ?? '[]';

        try {
            $returnValue = $this->jsonSerializer->unserialize($value);
        } catch (\InvalidArgumentException $e) {
            $returnValue = $value;
        } catch (Exception $e) {
            $returnValue = '';
        }

        return $returnValue;
    }

    /**
     * IsEnabledForWebsite
     *
     * @param string   $key
     * @param null|int $websiteId
     *
     * @return bool
     */
    public function isEnabledForWebsite(string $key, ?int $websiteId = null): bool
    {
        return $this->getConfigValue($key, $websiteId) === self::CONFIG_ENABLED;
    }

    /**
     * IsEnabledForStore
     *
     * @param string   $key
     * @param null|int $storeId
     *
     * @return bool
     */
    public function isEnabledForStore(string $key, ?int $storeId = null): bool
    {
        try {
            if (!$storeId) {
                $storeId = $this->storeManager->getStore()->getId();
            }

            $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();

            if (!$this->isEnabledForWebsite($key, $websiteId)) {
                return false;
            }

            $stores = $this->getConfigValue(self::STORE_SETTINGS, $websiteId);
            if (is_array($stores)) {
                foreach ($stores as $store) {
                    if ($store[StoreConfigInterface::STORE_ID_KEY] == $storeId) {
                        return true;
                    }
                }
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * CleanScope
     *
     * @return void
     */
    public function cleanScope(): void
    {
        $this->scopeConfig->clean();
    }

    /**
     * GetStoreSettings
     *
     * @return StoreConfigInterface[]
     */
    public function getStoreSettings(): array
    {
        return $this->getData(self::STORE_SETTINGS);
    }

    /**
     * SetStoreSettings
     *
     * @param StoreConfigInterface[] $storeSettings
     *
     * @return ConfigInterface
     */
    public function setStoreSettings(array $storeSettings): ConfigInterface
    {
        $this->setData(self::STORE_SETTINGS, $storeSettings);

        return $this;
    }

    /**
     * GetAvailableWebsites
     *
     * @return \Magento\Store\Api\Data\WebsiteInterface[]
     */
    public function getAvailableWebsites(): array
    {
        return $this->storeManager->getWebsites();
    }
}

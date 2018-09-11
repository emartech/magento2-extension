<?php

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\StoreConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config as ScopeConfig;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Store\Model\StoreManagerInterface;

use Emartech\Emarsys\Api\Data\ConfigInterface;

/**
 * Class Config
 * @package Emartech\Emarsys\Model\Data
 */
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
     * @return string
     */
    public function getStoreSlugs()
    {
        return $this->getData(self::STORE_SLUGS);
    }

    /**
     * @param string $storeSlugs
     *
     * @return $this
     */
    public function setStoreSlugs($storeSlugs)
    {
        $this->setData(self::STORE_SLUGS, $storeSlugs);

        return $this;
    }

    /**
     * @return string
     */
    public function getCollectCustomerEvents()
    {
        return $this->getData(self::CUSTOMER_EVENTS);
    }

    /**
     * @param string $collectCustomerEvents
     *
     * @return $this
     */
    public function setCollectCustomerEvents($collectCustomerEvents)
    {
        $this->setData(self::CUSTOMER_EVENTS, $collectCustomerEvents);

        return $this;
    }

    /**
     * @return string
     */
    public function getCollectSalesEvents()
    {
        return $this->getData(self::CUSTOMER_EVENTS);
    }

    /**
     * @param string $collectSalesEvents
     *
     * @return $this
     */
    public function setCollectSalesEvents($collectSalesEvents)
    {
        $this->setData(self::SALES_EVENTS, $collectSalesEvents);

        return $this;
    }

    /**
     * @return string
     */
    public function getCollectMarketingEvents()
    {
        return $this->getData(self::MARKETING_EVENTS);
    }

    /**
     * @param string $collectMarketingEvents
     *
     * @return $this
     */
    public function setCollectMarketingEvents($collectMarketingEvents)
    {
        $this->setData(self::MARKETING_EVENTS, $collectMarketingEvents);

        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantId()
    {
        return $this->getData(self::MERCHANT_ID);
    }

    /**
     * @param string $merchantId
     *
     * @return $this
     */
    public function setMerchantId($merchantId)
    {
        $this->setData(self::MERCHANT_ID, $merchantId);

        return $this;
    }

    /**
     * @return string
     */
    public function getInjectSnippet()
    {
        return $this->getData(self::INJECT_WEBEXTEND_SNIPPETS);
    }

    /**
     * @param string $injectSnippet
     *
     * @return $this
     */
    public function setInjectSnippet($injectSnippet)
    {
        $this->setData(self::INJECT_WEBEXTEND_SNIPPETS, $injectSnippet);

        return $this;
    }

    /**
     * @return string
     */
    public function getWebTrackingSnippetUrl()
    {
        return $this->getData(self::SNIPPET_URL);
    }

    /**
     * @param string $webTrackingSnippetUrl
     *
     * @return $this
     */
    public function setWebTrackingSnippetUrl($webTrackingSnippetUrl)
    {
        $this->setData(self::SNIPPET_URL, $webTrackingSnippetUrl);

        return $this;
    }

    /**
     * @param string $xmlPostPath
     * @param string $value
     * @param int    $scopeId
     * @param string $scope
     *
     * @return void
     */
    public function setConfigValue($xmlPostPath, $value, $scopeId, $scope = ConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        $xmlPath = self::XML_PATH_EMARSYS_PRE_TAG . trim($xmlPostPath, '/');

        if (is_array($value)) {
            $value = array_map(function ($item) {
                if ($item instanceof DataObject) {
                    $item = $item->toArray();
                }
                return $item;
            }, $value);
        }

        if (!is_string($value)) {
            $value = $this->jsonSerializer->serialize($value);
        }

        $this->configWriter->save($xmlPath, $value, $scope, $scopeId);
    }

    /**
     * @param string   $key
     * @param null|int $websiteId
     *
     * @return string
     */
    public function getConfigValue($key, $websiteId = null)
    {
        try {
            if (!$websiteId) {
                $websiteId = $this->storeManager->getWebsite()->getId();
            }

            return $this->scopeConfig->getValue('emartech/emarsys/config/' . $key, 'websites', $websiteId);
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * @param string   $key
     * @param null|int $websiteId
     *
     * @return bool
     */
    public function isEnabledForWebsite($key, $websiteId = 0)
    {
        return $this->getConfigValue($key, $websiteId) === self::CONFIG_ENABLED;
    }

    /**
     * @param string $key
     * @param int    $storeId
     *
     * @return bool
     */
    public function isEnabledForStore($key, $storeId)
    {
        try {
            $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();

            if (!$this->isEnabledForWebsite($key, $websiteId)) {
                return false;
            }

            $stores = $this->jsonSerializer->unserialize($this->getConfigValue(self::STORE_SETTINGS, $websiteId));

            foreach ($stores as $store) {
                if ($store[StoreConfigInterface::STORE_ID_KEY] == $storeId) {
                    return true;
                }
            }
        } catch (\Exception $e) { //@codingStandardsIgnoreLine
        }

        return false;
    }

    /**
     * @return void
     */
    public function cleanScope()
    {
        $this->scopeConfig->clean();
    }

    /**
     * @return \Emartech\Emarsys\Api\Data\StoreConfigInterface[]
     */
    public function getStoreSettings()
    {
        return $this->getData(self::STORE_SETTINGS);
    }

    /**
     * @param \Emartech\Emarsys\Api\Data\StoreConfigInterface[] $storeSettings
     *
     * @return $this
     */
    public function setStoreSettings($storeSettings)
    {
        $this->setData(self::STORE_SETTINGS, $storeSettings);

        return $this;
    }
}

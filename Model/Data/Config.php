<?php

namespace Emartech\Emarsys\Model\Data;

use Magento\Framework\DataObject;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config as ScopeConfig;

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
     * Config constructor.
     *
     * @param WriterInterface $configWriter
     * @param ScopeConfig     $scopeConfig
     * @param array           $data
     */
    public function __construct(
        WriterInterface $configWriter,
        ScopeConfig $scopeConfig,
        array $data = []
    ) {
        parent::__construct($data);

        $this->configWriter = $configWriter;
        $this->scopeConfig = $scopeConfig;
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
        $this->configWriter->save($xmlPath, $value, $scope, $scopeId);
    }

    /**
     * @return void
     */
    public function cleanScope()
    {
        $this->scopeConfig->clean();
    }
}

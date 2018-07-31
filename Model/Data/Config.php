<?php


namespace Emartech\Emarsys\Model\Data;


use Emartech\Emarsys\Api\Data\ConfigInterface;
use Magento\Framework\DataObject;

class Config extends DataObject implements ConfigInterface
{
  
  /**
   * @return string
   */
  public function getCollectCustomerEvents()
  {
    return $this->_data[ConfigInterface::CUSTOMER_EVENTS];
  }

  /**
   * @param string $collectCustomerEvents
   * @return Config
   */
  public function setCollectCustomerEvents($collectCustomerEvents)
  {
    $this->_data[ConfigInterface::CUSTOMER_EVENTS] = $collectCustomerEvents;
    return $this;
  }

  /**
   * @return string
   */
  public function getCollectSalesEvents()
  {
    return $this->_data[ConfigInterface::SALES_EVENTS];
  }

  /**
   * @param string $collectSalesEvents
   * @return Config
   */
  public function setCollectSalesEvents($collectSalesEvents)
  {
    $this->_data[ConfigInterface::SALES_EVENTS] = $collectSalesEvents;
    return $this;
  }

  /**
   * @return string
   */
  public function getCollectMarketingEvents()
  {
    return $this->_data[ConfigInterface::MARKETING_EVENTS];
  }

  /**
   * @param string $collectMarketingEvents
   * @return Config
   */
  public function setCollectMarketingEvents($collectMarketingEvents)
  {
    $this->_data[ConfigInterface::MARKETING_EVENTS] = $collectMarketingEvents;
    return $this;
  }

  /**
   * @return string
   */
  public function getMerchantId()
  {
    return $this->_data[ConfigInterface::MERCHANT_ID];
  }

  /**
   * @param string $merchantId
   * @return Config
   */
  public function setMerchantId($merchantId)
  {
    $this->_data[ConfigInterface::MERCHANT_ID] = $merchantId;
    return $this;
  }

  /**
   * @return string
   */
  public function getInjectSnippet()
  {
    return $this->_data[ConfigInterface::INJECT_WEBEXTEND_SNIPPETS];
  }

  /**
   * @param string $injectSnippet
   * @return Config
   */
  public function setInjectSnippet($injectSnippet)
  {
    $this->_data[ConfigInterface::INJECT_WEBEXTEND_SNIPPETS] = $injectSnippet;
    return $this;
  }

  /**
   * @return string
   */
  public function getWebTrackingSnippetUrl()
  {
    return $this->_data[ConfigInterface::SNIPPET_URL];
  }

  /**
   * @param string $webTrackingSnippetUrl
   * @return Config
   */
  public function setWebTrackingSnippetUrl($webTrackingSnippetUrl)
  {
    $this->_data[ConfigInterface::SNIPPET_URL] = $webTrackingSnippetUrl;
    return $this;
  }

}

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
    return $this->_data['collectCustomerEvents'];
  }

  /**
   * @param string $collectCustomerEvents
   * @return Config
   */
  public function setCollectCustomerEvents($collectCustomerEvents)
  {
    $this->_data['collectCustomerEvents'] = $collectCustomerEvents;
    return $this;
  }

  /**
   * @return string
   */
  public function getCollectSalesEvents()
  {
    return $this->_data['collectSalesEvents'];
  }

  /**
   * @param string $collectSalesEvents
   * @return Config
   */
  public function setCollectSalesEvents($collectSalesEvents)
  {
    $this->_data['collectSalesEvents'] = $collectSalesEvents;
    return $this;
  }

  /**
   * @return string
   */
  public function getCollectMarketingEvents()
  {
    return $this->_data['collectMarketingEvents'];
  }

  /**
   * @param string $collectMarketingEvents
   * @return Config
   */
  public function setCollectMarketingEvents($collectMarketingEvents)
  {
    $this->_data['collectMarketingEvents'] = $collectMarketingEvents;
    return $this;
  }

  /**
   * @return string
   */
  public function getMerchantId()
  {
    return $this->_data['merchantId'];
  }

  /**
   * @param string $merchantId
   * @return Config
   */
  public function setMerchantId($merchantId)
  {
    $this->_data['merchantId'] = $merchantId;
    return $this;
  }

  /**
   * @return string
   */
  public function getInjectSnippet()
  {
    return $this->_data['injectSnippet'];
  }

  /**
   * @param string $injectSnippet
   * @return Config
   */
  public function setInjectSnippet($injectSnippet)
  {
    $this->_data['injectSnippet'] = $injectSnippet;
    return $this;
  }

  /**
   * @return string
   */
  public function getWebTrackingSnippetUrl()
  {
    return $this->_data['webTrackingSnippetUrl'];
  }

  /**
   * @param string $webTrackingSnippetUrl
   * @return Config
   */
  public function setWebTrackingSnippetUrl($webTrackingSnippetUrl)
  {
    $this->_data['webTrackingSnippetUrl'] = $webTrackingSnippetUrl;
    return $this;
  }

}

<?php


namespace Emartech\Emarsys\Helper;


use Magento\Framework\App\Helper\AbstractHelper;
use Emartech\Emarsys\Api\Data\ConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;

class ConfigReader extends AbstractHelper
{
  /**
   * @var StoreManagerInterface
   */
  private $storeManager;

  public function __construct(
    Context $context,
    StoreManagerInterface $storeManager
  )
  {
    parent::__construct($context);
    $this->storeManager = $storeManager;
  }

  public function getConfigValue($key, $websiteId = null)
  {
    if (!$websiteId) {
      $websiteId = $this->storeManager->getWebsite()->getId();
    }
    return $this->scopeConfig->getValue('emartech/emarsys/config/' . $key, 'website', $websiteId);
  }

  public function isEnabled($key, $websiteId = 0)
  {
    return $this->getConfigValue($key, $websiteId) === ConfigInterface::CONFIG_ENABLED;
  }
}
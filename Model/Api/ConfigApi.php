<?php


namespace Emartech\Emarsys\Model\Api;


use Emartech\Emarsys\Api\ConfigApiInterface;
use Emartech\Emarsys\Api\Data\ConfigInterface;
use Magento\Framework\App\Config;
use Magento\Framework\App\Config\Storage\WriterInterface;

class ConfigApi implements ConfigApiInterface
{
  protected $defaultConfig = [
    ConfigInterface::CUSTOMER_EVENTS => 'disabled',
    ConfigInterface::SALES_EVENTS => 'disabled',
    ConfigInterface::MARKETING_EVENTS => 'disabled',
    ConfigInterface::INJECT_WEBEXTEND_SNIPPETS => 'disabled',
    ConfigInterface::MERCHANT_ID => null,
    ConfigInterface::SNIPPET_URL => null
  ];

  /** @var Config */
  protected $scopeConfig;

  /** @var WriterInterface */
  protected $configWriter;

  public function __construct(
    WriterInterface $configWriter,
    Config $scopeConfig
  )
  {
    $this->scopeConfig = $scopeConfig;
    $this->configWriter = $configWriter;
  }

  /**
   * @param int $websiteId
   * @param ConfigInterface $config
   * @return mixed
   */
  public function set(
    $websiteId,
    ConfigInterface $config
  )
  {
    foreach ($config->getData() as $key => $value) {
      $this->configWriter->save('emartech/emarsys/config/' . $key, $value, 'websites', $websiteId);
    }
    $this->scopeConfig->clean();
    return 'OK';
  }

  /**
   * @param int $websiteId
   * @return mixed
   */
  public function setDefault($websiteId)
  {
    foreach ($this->defaultConfig as $key => $value) {
      $this->configWriter->save('emartech/emarsys/config/' . $key, $value, 'websites', $websiteId);
    }
    $this->scopeConfig->clean();
    return 'OK';
  }
}

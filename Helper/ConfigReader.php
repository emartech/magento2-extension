<?php

namespace Emartech\Emarsys\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

use Emartech\Emarsys\Api\Data\ConfigInterface;

/**
 * Class ConfigReader
 * @package Emartech\Emarsys\Helper
 */
class ConfigReader extends AbstractHelper
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * ConfigReader constructor.
     *
     * @param Context         $context
     * @param ConfigInterface $config
     */
    public function __construct(
        Context $context,
        ConfigInterface $config
    ) {
        parent::__construct($context);
        $this->config = $config;
    }

    /**
     * @param string   $key
     * @param null\int $websiteId
     *
     * @return string
     */
    public function getConfigValue($key, int $websiteId = null)
    {
        return $this->config->getConfigValue($key, $websiteId);
    }

    /**
     * @param string $key
     * @param int    $websiteId
     *
     * @return bool
     */
    public function isEnabledForWebsite($key, $websiteId = 0)
    {
        return $this->config->isEnabledForWebsite($key, $websiteId);
    }

    /**
     * @param string $key
     * @param int    $storeId
     *
     * @return bool
     */
    public function isEnableForStore($key, $storeId)
    {
        return $this->config->isEnabledForStore($key, $storeId);
    }

    /**
     * @return string
     */
    public function isEnabledForStore($key, $storeId = 0)
    {
        $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();

        return $this->isStoreEnabled($websiteId, $storeId)
            && $this->isEnabledForWebsite($key, $websiteId);
    }

    /**
     * @param $websiteId
     * @param int $storeId
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function isStoreEnabled($websiteId, int $storeId)
    {
        $stores = json_decode($this->getConfigValue(ConfigInterface::STORE_SLUGS, $websiteId), true);
        if (is_array($stores)) {
            foreach($stores as $store) {
                if ( $store['id'] === $storeId ) {
                    return true;
                }
            }
        }

        return false;
    }
}

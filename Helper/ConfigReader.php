<?php

namespace Emartech\Emarsys\Helper;

use Emartech\Emarsys\Api\Data\ConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

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
     * GetConfigValue
     *
     * @param string   $key
     * @param null|int $websiteId
     *
     * @return string|string[]
     */
    public function getConfigValue(string $key, ?int $websiteId = null)
    {
        return $this->config->getConfigValue($key, $websiteId);
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
        return $this->config->isEnabledForWebsite($key, $websiteId);
    }

    /**
     * IsEnabledForStore
     *
     * @param string   $key
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isEnabledForStore(string $key, ?int $storeId = null): bool
    {
        return $this->config->isEnabledForStore($key, $storeId);
    }

    /**
     * GetModuleName
     *
     * @return string
     */
    public function getModuleName(): string
    {
        return $this->_getModuleName();
    }
}

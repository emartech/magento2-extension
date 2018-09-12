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
    public function getConfigValue($key, $websiteId = null)
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
    public function getModuleName()
    {
        return $this->_getModuleName();
    }
}

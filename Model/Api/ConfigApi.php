<?php

namespace Emartech\Emarsys\Model\Api;

use Emartech\Emarsys\Api\ConfigApiInterface;
use Emartech\Emarsys\Api\Data\ConfigInterface;
use Emartech\Emarsys\Api\Data\ConfigInterfaceFactory;
use Emartech\Emarsys\Api\Data\StatusResponseInterfaceFactory;
use Emartech\Emarsys\Api\Data\StatusResponseInterface;

/**
 * Class ConfigApi
 * @package Emartech\Emarsys\Model\Api
 */
class ConfigApi implements ConfigApiInterface
{
    /**
     * @var array
     */
    private $defaultConfig = [
        ConfigInterface::CUSTOMER_EVENTS           => ConfigInterface::CONFIG_DISABLED,
        ConfigInterface::SALES_EVENTS              => ConfigInterface::CONFIG_DISABLED,
        ConfigInterface::MARKETING_EVENTS          => ConfigInterface::CONFIG_DISABLED,
        ConfigInterface::INJECT_WEBEXTEND_SNIPPETS => ConfigInterface::CONFIG_DISABLED,
        ConfigInterface::MERCHANT_ID               => ConfigInterface::CONFIG_EMPTY,
        ConfigInterface::SNIPPET_URL               => ConfigInterface::CONFIG_EMPTY,
    ];

    /**
     * @var ConfigInterfaceFactory
     */
    private $configFactory;

    /**
     * @var StatusResponseInterfaceFactory
     */
    private $statusResponseFactory;

    /**
     * ConfigApi constructor.
     *
     * @param ConfigInterfaceFactory         $configFactory
     * @param StatusResponseInterfaceFactory $statusResponseFactory
     */
    public function __construct(
        ConfigInterfaceFactory $configFactory,
        StatusResponseInterfaceFactory $statusResponseFactory
    ) {
        $this->configFactory = $configFactory;
        $this->statusResponseFactory = $statusResponseFactory;
    }

    /**
     * @param int             $websiteId
     * @param ConfigInterface $config
     *
     * @return StatusResponseInterface
     */
    public function set($websiteId, ConfigInterface $config): StatusResponseInterface
    {
        foreach ($config->getData() as $key => $value) {
            $config->setConfigValue($key, $value, $websiteId);
        }
        $config->cleanScope();

        return $this->statusResponseFactory->create()
            ->setStatus('ok');
    }

    /**
     * @param int $websiteId
     *
     * @return StatusResponseInterface
     */
    public function setDefault($websiteId): StatusResponseInterface
    {
        /** @var ConfigInterface $config */
        $config = $this->configFactory->create();

        foreach ($this->defaultConfig as $key => $value) {
            $config->setConfigValue($key, $value, $websiteId);
        }
        $config->cleanScope();

        return $this->statusResponseFactory->create()
            ->setStatus('ok');
    }
}

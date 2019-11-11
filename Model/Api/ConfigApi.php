<?php

namespace Emartech\Emarsys\Model\Api;

use Emartech\Emarsys\Api\AttributesApiInterface;
use Emartech\Emarsys\Api\ConfigApiInterface;
use Emartech\Emarsys\Api\Data\ConfigInterface;
use Emartech\Emarsys\Api\Data\ConfigInterfaceFactory;
use Emartech\Emarsys\Api\Data\StatusResponseInterface;
use Emartech\Emarsys\Api\Data\StatusResponseInterfaceFactory;
use Magento\Framework\Webapi\Exception as WebApiException;

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
    public function set($websiteId, ConfigInterface $config)
    {
        $foundDifference = false;

        foreach ($config->getData() as $key => $value) {
            if ($config->setConfigValue($key, $value, $websiteId)) {
                $foundDifference = true;
            }
        }

        if ($foundDifference) {
            $config->cleanScope();
        }

        return $this->statusResponseFactory->create()
            ->setStatus('ok');
    }

    /**
     * @param int $websiteId
     *
     * @return StatusResponseInterface
     */
    public function setDefault($websiteId)
    {
        /** @var ConfigInterface $config */
        $config = $this->configFactory->create();

        $foundDifference = false;

        foreach ($this->defaultConfig as $key => $value) {
            if ($config->setConfigValue($key, $value, $websiteId)) {
                $foundDifference = true;
            }
        }

        if ($foundDifference) {
            $config->cleanScope();
        }

        return $this->statusResponseFactory->create()
            ->setStatus('ok');
    }

    /**
     * @param string   $type
     * @param int      $websiteId
     * @param string[] $codes
     *
     * @return StatusResponseInterface
     * @throws WebApiException
     */
    public function setAttributes($type, $websiteId, $codes)
    {
        if (!in_array($type, AttributesApiInterface::TYPES)) {
            throw new WebApiException(__('Invalid Type'));
        }

        /** @var ConfigInterface $config */
        $config = $this->configFactory->create();

        if (0 !== $websiteId && !array_key_exists($websiteId, $config->getAvailableWebsites())) {
            throw new WebApiException(__('Invalid Website'));
        }

        if ($config->setConfigValue($type . ConfigInterface::ATTRIBUTE_CONFIG_POST_TAG, $codes, $websiteId)) {
            $config->cleanScope();
        }

        return $this->statusResponseFactory->create()
            ->setStatus('ok');
    }
}

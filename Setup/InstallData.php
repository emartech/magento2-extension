<?php

namespace Emartech\Emarsys\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Emartech\Emarsys\Helper\Integration;

/**
 * Class InstallData
 * @package Emartech\Emarsys\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var Integration
     */
    private $integration;

    /**
     * InstallData constructor.
     *
     * @param Integration $integration
     */
    public function __construct(Integration $integration)
    {
        $this->integration = $integration;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     *
     * @throws \Magento\Framework\Oauth\Exception
     */
    // @codingStandardsIgnoreLine
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->integration->create();
        $this->integration->saveConnectTokenToConfig();

        $setup->endSetup();
    }
}

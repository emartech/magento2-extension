<?php

namespace Emartech\Emarsys\Setup;

use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Emartech\Emarsys\Helper\Integration;

/**
 * Class Uninstall
 * @package Emartech\Emarsys\Setup
 */
class Uninstall implements UninstallInterface
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
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     *
     * @return void
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->integration->delete();

        $setup->endSetup();
    }
}

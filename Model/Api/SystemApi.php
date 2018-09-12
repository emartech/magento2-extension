<?php

namespace Emartech\Emarsys\Model\Api;

use Magento\Framework\App\ProductMetadata;
use Magento\Framework\Module\ModuleListInterface;

use Emartech\Emarsys\Api\SystemApiInterface;
use Emartech\Emarsys\Api\Data\SystemApiResponseInterface;
use Emartech\Emarsys\Api\Data\SystemApiResponseInterfaceFactory;
use Emartech\Emarsys\Helper\ConfigReader;

/**
 * Class SystemApi
 * @package Emartech\Emarsys\Model\Api
 */
class SystemApi implements SystemApiInterface
{
    /**
     * @var ProductMetadata
     */
    private $productMetadata;

    /**
     * @var SystemApiResponseInterfaceFactory
     */
    private $systemApiResponseFactory;

    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @var ConfigReader
     */
    private $configReader;

    /**
     * SystemApi constructor.
     *
     * @param ProductMetadata                   $productMetadata
     * @param SystemApiResponseInterfaceFactory $systemApiResponseFactory
     * @param ModuleListInterface               $moduleList
     * @param ConfigReader                      $configReader
     */
    public function __construct(
        ProductMetadata $productMetadata,
        SystemApiResponseInterfaceFactory $systemApiResponseFactory,
        ModuleListInterface $moduleList,
        ConfigReader $configReader
    ) {
        $this->productMetadata = $productMetadata;
        $this->systemApiResponseFactory = $systemApiResponseFactory;
        $this->moduleList = $moduleList;
        $this->configReader = $configReader;
    }

    /**
     * @return SystemApiResponseInterface
     */
    public function get(): SystemApiResponseInterface
    {
        return $this->systemApiResponseFactory->create()
            ->setMagentoVersion($this->getMagentoVersion())
            ->setPhpVersion($this->getPhpVersion())
            ->setModuleVersion($this->getModuleVersion());
    }

    /**
     * @return string
     */
    private function getMagentoVersion(): string
    {
        return $this->productMetadata->getVersion();
    }

    /**
     * @return string
     */
    private function getPhpVersion(): string
    {
        if (defined(PHP_VERSION)) {
            return PHP_VERSION;
        }

        return phpversion();
    }

    /**
     * @return string
     */
    private function getModuleVersion(): string
    {
        $moduleData = $this->moduleList->getOne($this->configReader->getModuleName());
        if (is_array($moduleData) && array_key_exists('setup_version', $moduleData)) {
            return $moduleData['setup_version'];
        }

        return '';
    }
}

<?php

namespace Emartech\Emarsys\Model\Api;

use Emartech\Emarsys\Api\Data\SystemApiResponseInterface;
use Emartech\Emarsys\Api\Data\SystemApiResponseInterfaceFactory;
use Emartech\Emarsys\Api\SystemApiInterface;
use Emartech\Emarsys\Helper\ConfigReader;
use Magento\Customer\Model\Config\Share as ConfigShare;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Module\ModuleListInterface;

class SystemApi implements SystemApiInterface
{
    /**
     * @var ProductMetadataInterface
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
     * @var ConfigShare
     */
    private $configShare;

    /**
     * SystemApi constructor.
     *
     * @param ProductMetadataInterface          $productMetadata
     * @param SystemApiResponseInterfaceFactory $systemApiResponseFactory
     * @param ModuleListInterface               $moduleList
     * @param ConfigReader                      $configReader
     * @param ConfigShare                       $configShare
     */
    public function __construct(
        ProductMetadataInterface $productMetadata,
        SystemApiResponseInterfaceFactory $systemApiResponseFactory,
        ModuleListInterface $moduleList,
        ConfigReader $configReader,
        ConfigShare $configShare
    ) {
        $this->productMetadata = $productMetadata;
        $this->systemApiResponseFactory = $systemApiResponseFactory;
        $this->moduleList = $moduleList;
        $this->configReader = $configReader;
        $this->configShare = $configShare;
    }

    /**
     * Get
     *
     * @return SystemApiResponseInterface
     */
    public function get(): SystemApiResponseInterface
    {
        return $this->systemApiResponseFactory
            ->create()
            ->setMagentoVersion($this->getMagentoVersion())
            ->setMagentoEdition($this->getMagentoEdition())
            ->setPhpVersion($this->getPhpVersion())
            ->setModuleVersion($this->getModuleVersion())
            ->setIsWebsiteScope($this->configShare->isWebsiteScope());
    }

    /**
     * GetMagentoVersion
     *
     * @return string
     */
    private function getMagentoVersion(): string
    {
        return $this->productMetadata->getVersion();
    }

    /**
     * GetMagentoEdition
     *
     * @return string
     */
    private function getMagentoEdition(): string
    {
        return $this->productMetadata->getEdition();
    }

    /**
     * GetPhpVersion
     *
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
     * GetModuleVersion
     *
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

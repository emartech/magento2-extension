<?php

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\SystemApiResponseInterface;
use Magento\Framework\DataObject;

class SystemApiResponse extends DataObject implements SystemApiResponseInterface
{
    /**
     * GetMagentoVersion
     *
     * @return string
     */
    public function getMagentoVersion(): string
    {
        return (string) $this->getData(self::MAGENTO_VERSION_KEY);
    }

    /**
     * GetModuleVersion
     *
     * @return string
     */
    public function getModuleVersion(): string
    {
        return (string) $this->getData(self::MODULE_VERSION_KEY);
    }

    /**
     * GetPhpVersion
     *
     * @return string
     */
    public function getPhpVersion(): string
    {
        return (string) $this->getData(self::PHP_VERSION_KEY);
    }

    /**
     * GetMagentoEdition
     *
     * @return string
     */
    public function getMagentoEdition(): string
    {
        return (string) $this->getData(self::MAGENTO_EDITION_KEY);
    }

    /**
     * GetIsWebsiteScope
     *
     * @return bool
     */
    public function getIsWebsiteScope(): bool
    {
        return (bool) $this->getData(self::IS_WEBSITE_SCOPE_KEY);
    }

    /**
     * SetMagentoVersion
     *
     * @param string $magentoVersion
     *
     * @return SystemApiResponseInterface
     */
    public function setMagentoVersion(string $magentoVersion): SystemApiResponseInterface
    {
        $this->setData(self::MAGENTO_VERSION_KEY, $magentoVersion);

        return $this;
    }

    /**
     * SetModuleVersion
     *
     * @param string $moduleVersion
     *
     * @return SystemApiResponseInterface
     */
    public function setModuleVersion(string $moduleVersion): SystemApiResponseInterface
    {
        $this->setData(self::MODULE_VERSION_KEY, $moduleVersion);

        return $this;
    }

    /**
     * SetPhpVersion
     *
     * @param string $phpVersion
     *
     * @return SystemApiResponseInterface
     */
    public function setPhpVersion(string $phpVersion): SystemApiResponseInterface
    {
        $this->setData(self::PHP_VERSION_KEY, $phpVersion);

        return $this;
    }

    /**
     * SetMagentoEdition
     *
     * @param string $magentoEdition
     *
     * @return SystemApiResponseInterface
     */
    public function setMagentoEdition(string $magentoEdition): SystemApiResponseInterface
    {
        $this->setData(self::MAGENTO_EDITION_KEY, $magentoEdition);

        return $this;
    }

    /**
     * SetIsWebsiteScope
     *
     * @param bool $isWebsiteScope
     *
     * @return SystemApiResponseInterface
     */
    public function setIsWebsiteScope(bool $isWebsiteScope): SystemApiResponseInterface
    {
        $this->setData(self::IS_WEBSITE_SCOPE_KEY, $isWebsiteScope);

        return $this;
    }
}

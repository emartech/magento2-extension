<?php

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\SystemApiResponseInterface;
use Magento\Framework\DataObject;

class SystemApiResponse extends DataObject implements SystemApiResponseInterface
{
    /**
     * GetMagentoVersion
     *
     * @return string|null
     */
    public function getMagentoVersion(): ?string
    {
        return $this->getData(self::MAGENTO_VERSION_KEY);
    }

    /**
     * GetModuleVersion
     *
     * @return string|null
     */
    public function getModuleVersion(): ?string
    {
        return $this->getData(self::MODULE_VERSION_KEY);
    }

    /**
     * GetPhpVersion
     *
     * @return string|null
     */
    public function getPhpVersion(): ?string
    {
        return $this->getData(self::PHP_VERSION_KEY);
    }

    /**
     * GetMagentoEdition
     *
     * @return string|null
     */
    public function getMagentoEdition(): ?string
    {
        return $this->getData(self::MAGENTO_EDITION_KEY);
    }

    /**
     * GetIsWebsiteScope
     *
     * @return bool|null
     */
    public function getIsWebsiteScope(): ?bool
    {
        return $this->getData(self::IS_WEBSITE_SCOPE_KEY);
    }

    /**
     * SetMagentoVersion
     *
     * @param string|null $magentoVersion
     *
     * @return SystemApiResponseInterface
     */
    public function setMagentoVersion(?string $magentoVersion = null): SystemApiResponseInterface
    {
        $this->setData(self::MAGENTO_VERSION_KEY, $magentoVersion);

        return $this;
    }

    /**
     * SetModuleVersion
     *
     * @param string|null $moduleVersion
     *
     * @return SystemApiResponseInterface
     */
    public function setModuleVersion(?string $moduleVersion = null): SystemApiResponseInterface
    {
        $this->setData(self::MODULE_VERSION_KEY, $moduleVersion);

        return $this;
    }

    /**
     * SetPhpVersion
     *
     * @param string|null $phpVersion
     *
     * @return SystemApiResponseInterface
     */
    public function setPhpVersion(?string $phpVersion = null): SystemApiResponseInterface
    {
        $this->setData(self::PHP_VERSION_KEY, $phpVersion);

        return $this;
    }

    /**
     * SetMagentoEdition
     *
     * @param string|null $magentoEdition
     *
     * @return SystemApiResponseInterface
     */
    public function setMagentoEdition(?string $magentoEdition = null): SystemApiResponseInterface
    {
        $this->setData(self::MAGENTO_EDITION_KEY, $magentoEdition);

        return $this;
    }

    /**
     * SetIsWebsiteScope
     *
     * @param bool|null $isWebsiteScope
     *
     * @return SystemApiResponseInterface
     */
    public function setIsWebsiteScope(?bool $isWebsiteScope = null): SystemApiResponseInterface
    {
        $this->setData(self::IS_WEBSITE_SCOPE_KEY, $isWebsiteScope);

        return $this;
    }
}

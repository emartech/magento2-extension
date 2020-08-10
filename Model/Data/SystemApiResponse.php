<?php

namespace Emartech\Emarsys\Model\Data;

use Magento\Framework\DataObject;

use Emartech\Emarsys\Api\Data\SystemApiResponseInterface;

class SystemApiResponse extends DataObject implements SystemApiResponseInterface
{
    /**
     * @return string
     */
    public function getMagentoVersion()
    {
        return $this->getData(self::MAGENTO_VERSION_KEY);
    }

    /**
     * @return string
     */
    public function getModuleVersion()
    {
        return $this->getData(self::MODULE_VERSION_KEY);
    }

    /**
     * @return string
     */
    public function getPhpVersion()
    {
        return $this->getData(self::PHP_VERSION_KEY);
    }

    /**
     * @return string
     */
    public function getMagentoEdition()
    {
        return $this->getData(self::MAGENTO_EDITION_KEY);
    }

    /**
     * @return bool
     */
    public function getIsWebsiteScope()
    {
        return $this->getData(self::IS_WEBSITE_SCOPE_KEY);
    }

    /**
     * @param string $magentoVersion
     *
     * @return $this
     */
    public function setMagentoVersion($magentoVersion)
    {
        $this->setData(self::MAGENTO_VERSION_KEY, $magentoVersion);

        return $this;
    }

    /**
     * @param string $moduleVersion
     *
     * @return $this
     */
    public function setModuleVersion($moduleVersion)
    {
        $this->setData(self::MODULE_VERSION_KEY, $moduleVersion);

        return $this;
    }

    /**
     * @param string $phpVersion
     *
     * @return $this
     */
    public function setPhpVersion($phpVersion)
    {
        $this->setData(self::PHP_VERSION_KEY, $phpVersion);

        return $this;
    }

    /**
     * @param string $magentoEdition
     *
     * @return $this
     */
    public function setMagentoEdition($magentoEdition)
    {
        $this->setData(self::MAGENTO_EDITION_KEY, $magentoEdition);

        return $this;
    }

    /**
     * @param bool $isWebsiteScope
     *
     * @return $this
     */
    public function setIsWebsiteScope($isWebsiteScope)
    {
        $this->setData(self::IS_WEBSITE_SCOPE_KEY, $isWebsiteScope);

        return $this;
    }
}

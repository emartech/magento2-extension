<?php

namespace Emartech\Emarsys\Model\Data;

use Magento\Framework\DataObject;

use Emartech\Emarsys\Api\Data\SystemApiResponseInterface;

/**
 * Class SystemApiResponse
 * @package Emartech\Emarsys\Model\Data
 */
class SystemApiResponse extends DataObject implements SystemApiResponseInterface
{
    /**
     * @return string
     */
    public function getMagentoVersion(): string
    {
        return $this->getData(self::MAGENTO_VERSION_KEY);
    }

    /**
     * @return string
     */
    public function getModuleVersion(): string
    {
        return $this->getData(self::MODULE_VERSION_KEY);
    }

    /**
     * @return string
     */
    public function getPhpVersion(): string
    {
        return $this->getData(self::PHP_VERSION_KEY);
    }

    /**
     * @param string $magentoVersion
     *
     * @return $this
     */
    public function setMagentoVersion($magentoVersion): SystemApiResponseInterface
    {
        $this->setData(self::MAGENTO_VERSION_KEY, $magentoVersion);

        return $this;
    }

    /**
     * @param string $moduleVersion
     *
     * @return $this
     */
    public function setModuleVersion($moduleVersion): SystemApiResponseInterface
    {
        $this->setData(self::MODULE_VERSION_KEY, $moduleVersion);

        return $this;
    }

    /**
     * @param string $phpVersion
     *
     * @return $this
     */
    public function setPhpVersion($phpVersion): SystemApiResponseInterface
    {
        $this->setData(self::PHP_VERSION_KEY, $phpVersion);

        return $this;
    }
}

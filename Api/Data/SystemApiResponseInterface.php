<?php

namespace Emartech\Emarsys\Api\Data;

/**
 * Interface SystemApiResponseInterface
 * @package Emartech\Emarsys\Api\Data
 */
interface SystemApiResponseInterface
{
    const MAGENTO_VERSION_KEY = 'magento_version';
    const PHP_VERSION_KEY     = 'php_version';
    const MODULE_VERSION_KEY  = 'module_version';

    /**
     * @return string
     */
    public function getMagentoVersion(): string;

    /**
     * @return string
     */
    public function getPhpVersion(): string;

    /**
     * @return string
     */
    public function getModuleVersion(): string;
    
    /**
     * @param string $magentoVersion
     *
     * @return $this
     */
    public function setMagentoVersion($magentoVersion): SystemApiResponseInterface;

    /**
     * @param string $phpVersion
     *
     * @return $this
     */
    public function setPhpVersion($phpVersion): SystemApiResponseInterface;

    /**
     * @param string $moduleVersion
     *
     * @return $this
     */
    public function setModuleVersion($moduleVersion): SystemApiResponseInterface;
}

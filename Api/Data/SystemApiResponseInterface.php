<?php

namespace Emartech\Emarsys\Api\Data;

interface SystemApiResponseInterface
{
    public const MAGENTO_VERSION_KEY  = 'magento_version';
    public const PHP_VERSION_KEY      = 'php_version';
    public const MODULE_VERSION_KEY   = 'module_version';
    public const MAGENTO_EDITION_KEY  = 'magento_edition';
    public const IS_WEBSITE_SCOPE_KEY = 'is_website_scope';

    /**
     * GetMagentoVersion
     *
     * @return string
     */
    public function getMagentoVersion(): string;

    /**
     * GetPhpVersion
     *
     * @return string
     */
    public function getPhpVersion(): string;

    /**
     * GetModuleVersion
     *
     * @return string
     */
    public function getModuleVersion(): string;

    /**
     * GetMagentoEdition
     *
     * @return string
     */
    public function getMagentoEdition(): string;

    /**
     * GetIsWebsiteScope
     *
     * @return bool
     */
    public function getIsWebsiteScope(): bool;

    /**
     * SetMagentoVersion
     *
     * @param string $magentoVersion
     *
     * @return \Emartech\Emarsys\Api\Data\SystemApiResponseInterface
     */
    public function setMagentoVersion(string $magentoVersion): SystemApiResponseInterface;

    /**
     * SetPhpVersion
     *
     * @param string $phpVersion
     *
     * @return \Emartech\Emarsys\Api\Data\SystemApiResponseInterface
     */
    public function setPhpVersion(string $phpVersion): SystemApiResponseInterface;

    /**
     * SetModuleVersion
     *
     * @param string $moduleVersion
     *
     * @return \Emartech\Emarsys\Api\Data\SystemApiResponseInterface
     */
    public function setModuleVersion(string $moduleVersion): SystemApiResponseInterface;

    /**
     * SetMagentoEdition
     *
     * @param string $magentoEdition
     *
     * @return \Emartech\Emarsys\Api\Data\SystemApiResponseInterface
     */
    public function setMagentoEdition(string $magentoEdition): SystemApiResponseInterface;

    /**
     * SetIsWebsiteScope
     *
     * @param bool $isWebsiteScope
     *
     * @return \Emartech\Emarsys\Api\Data\SystemApiResponseInterface
     */
    public function setIsWebsiteScope(bool $isWebsiteScope): SystemApiResponseInterface;
}

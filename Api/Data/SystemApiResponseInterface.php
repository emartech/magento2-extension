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
     * @return string|null
     */
    public function getMagentoVersion(): ?string;

    /**
     * GetPhpVersion
     *
     * @return string|null
     */
    public function getPhpVersion(): ?string;

    /**
     * GetModuleVersion
     *
     * @return string|null
     */
    public function getModuleVersion(): ?string;

    /**
     * GetMagentoEdition
     *
     * @return string|null
     */
    public function getMagentoEdition(): ?string;

    /**
     * GetIsWebsiteScope
     *
     * @return bool|null
     */
    public function getIsWebsiteScope(): ?bool;

    /**
     * SetMagentoVersion
     *
     * @param string|null $magentoVersion
     *
     * @return \Emartech\Emarsys\Api\Data\SystemApiResponseInterface
     */
    public function setMagentoVersion(?string $magentoVersion = null): SystemApiResponseInterface;

    /**
     * SetPhpVersion
     *
     * @param string|null $phpVersion
     *
     * @return \Emartech\Emarsys\Api\Data\SystemApiResponseInterface
     */
    public function setPhpVersion(?string $phpVersion = null): SystemApiResponseInterface;

    /**
     * SetModuleVersion
     *
     * @param string|null $moduleVersion
     *
     * @return \Emartech\Emarsys\Api\Data\SystemApiResponseInterface
     */
    public function setModuleVersion(?string $moduleVersion = null): SystemApiResponseInterface;

    /**
     * SetMagentoEdition
     *
     * @param string|null $magentoEdition
     *
     * @return \Emartech\Emarsys\Api\Data\SystemApiResponseInterface
     */
    public function setMagentoEdition(?string $magentoEdition = null): SystemApiResponseInterface;

    /**
     * SetIsWebsiteScope
     *
     * @param bool|null $isWebsiteScope
     *
     * @return \Emartech\Emarsys\Api\Data\SystemApiResponseInterface
     */
    public function setIsWebsiteScope(?bool $isWebsiteScope = null): SystemApiResponseInterface;
}

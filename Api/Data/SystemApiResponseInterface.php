<?php

namespace Emartech\Emarsys\Api\Data;

interface SystemApiResponseInterface
{
    const MAGENTO_VERSION_KEY = 'magento_version';
    const PHP_VERSION_KEY     = 'php_version';
    const MODULE_VERSION_KEY  = 'module_version';
    const MAGENTO_EDITION_KEY  = 'magento_edition';
    const IS_WEBSITE_SCOPE_KEY  = 'is_website_scope';

    /**
     * @return string
     */
    public function getMagentoVersion();

    /**
     * @return string
     */
    public function getPhpVersion();

    /**
     * @return string
     */
    public function getModuleVersion();

    /**
     * @return string
     */
    public function getMagentoEdition();

    /**
     * @return bool
     */
    public function getIsWebsiteScope();
    
    /**
     * @param string $magentoVersion
     *
     * @return $this
     */
    public function setMagentoVersion($magentoVersion);

    /**
     * @param string $phpVersion
     *
     * @return $this
     */
    public function setPhpVersion($phpVersion);

    /**
     * @param string $moduleVersion
     *
     * @return $this
     */
    public function setModuleVersion($moduleVersion);

    /**
     * @param string $magentoEdition
     *
     * @return $this
     */
    public function setMagentoEdition($magentoEdition);

    /**
     * @param bool $isWebsiteScope
     *
     * @return $this
     */
    public function setIsWebsiteScope($isWebsiteScope);
}

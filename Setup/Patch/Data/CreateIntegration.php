<?php
/**
 * Copyright ©2022 ITG Commerce. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @author Tamás Perencz <tamas.perencz@itgcommerce.com>
 */

namespace Emartech\Emarsys\Setup\Patch\Data;

use Emartech\Emarsys\Helper\Integration;
use Magento\Framework\Exception\IntegrationException;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

class CreateIntegration implements DataPatchInterface, PatchRevertableInterface
{
    /**
     * @var Integration
     */
    private $integration;

    /**
     * @param Integration $integration
     */
    public function __construct(Integration $integration)
    {
        $this->integration = $integration;
    }

    /**
     * Apply
     *
     * @return void
     */
    public function apply()
    {
        $this->integration->create();
    }

    /**
     * Revert
     *
     * @return void
     * @throws IntegrationException
     */
    public function revert()
    {
        $this->integration->delete();
    }

    /**
     * GetDependencies
     *
     * @return array
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * GetAliases
     *
     * @return array
     */
    public function getAliases()
    {
        return [];
    }
}

<?php
/**
 * Copyright ©2019 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Api;

use Emartech\Emarsys\Api\Data\InventoryApiResponseInterface;

interface InventoryApiInterface
{
    /**
     * GetList
     *
     * @param string[] $sku
     *
     * @return \Emartech\Emarsys\Api\Data\InventoryApiResponseInterface
     */
    public function getList(array $sku): InventoryApiResponseInterface;
}

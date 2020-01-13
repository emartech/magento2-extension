<?php
/**
 * Copyright ©2019 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Api;

interface InventoryApiInterface
{
    /**
     * @param string[] $sku
     *
     * @return \Emartech\Emarsys\Api\Data\InventoryApiResponseInterface
     */
    public function getList($sku);
}

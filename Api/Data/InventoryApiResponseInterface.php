<?php
/**
 * Copyright ©2019 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Api\Data;

interface InventoryApiResponseInterface
{
    const ITEMS_KEY = 'items';

    /**
     * @return \Emartech\Emarsys\Api\Data\InventoryItemInterface[]
     */
    public function getItems();

    /**
     * @param \Emartech\Emarsys\Api\Data\InventoryItemInterface[] $items
     *
     * @return $this
     */
    public function setItems(array $items);
}

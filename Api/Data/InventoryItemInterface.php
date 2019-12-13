<?php
/**
 * Copyright ©2019 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Api\Data;


interface InventoryItemInterface
{
    const SKU_KEY = 'sku';
    const INVENTORY_ITEMS_KEY = 'inventory_items';

    /**
     * @return string
     */
    public function getSku();

    /**
     * @param string $sku
     *
     * @return $this
     */
    public function setSku($sku);

    /**
     * @return \Emartech\Emarsys\Api\Data\InventoryItemItemInterface[]
     */
    public function getInventoryItems();

    /**
     * @param \Emartech\Emarsys\Api\Data\InventoryItemItemInterface[] $inventoryItems
     *
     * @return $this
     */
    public function setInventoryItems($inventoryItems);
}

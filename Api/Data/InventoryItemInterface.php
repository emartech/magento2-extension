<?php
/**
 * Copyright ©2019 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Api\Data;

interface InventoryItemInterface
{
    public const SKU_KEY             = 'sku';
    public const INVENTORY_ITEMS_KEY = 'inventory_items';

    /**
     * GetSku
     *
     * @return string|null
     */
    public function getSku(): ?string;

    /**
     * SetSku
     *
     * @param string|null $sku
     *
     * @return \Emartech\Emarsys\Api\Data\InventoryItemInterface
     */
    public function setSku(?string $sku = null): InventoryItemInterface;

    /**
     * GetInventoryItems
     *
     * @return \Emartech\Emarsys\Api\Data\InventoryItemItemInterface[]|null
     */
    public function getInventoryItems(): ?array;

    /**
     * SetInventoryItems
     *
     * @param \Emartech\Emarsys\Api\Data\InventoryItemItemInterface[]|null $inventoryItems
     *
     * @return \Emartech\Emarsys\Api\Data\InventoryItemInterface
     */
    public function setInventoryItems(?array $inventoryItems = null): InventoryItemInterface;
}

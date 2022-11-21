<?php
/**
 * Copyright ©2019 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Api\Data;

interface InventoryApiResponseInterface
{
    public const ITEMS_KEY = 'items';

    /**
     * GetItems
     *
     * @return \Emartech\Emarsys\Api\Data\InventoryItemInterface[]
     */
    public function getItems(): array;

    /**
     * SetItems
     *
     * @param \Emartech\Emarsys\Api\Data\InventoryItemInterface[] $items
     *
     * @return \Emartech\Emarsys\Api\Data\InventoryApiResponseInterface
     */
    public function setItems(array $items): InventoryApiResponseInterface;
}

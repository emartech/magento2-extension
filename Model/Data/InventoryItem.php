<?php

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\InventoryItemInterface;
use Emartech\Emarsys\Api\Data\InventoryItemItemInterface;
use Magento\Framework\DataObject;

class InventoryItem extends DataObject implements InventoryItemInterface
{
    /**
     * GetSku
     *
     * @return string|null
     */
    public function getSku(): ?string
    {
        return $this->getData(self::SKU_KEY);
    }

    /**
     * GetInventoryItems
     *
     * @return InventoryItemItemInterface[]|null
     */
    public function getInventoryItems(): ?array
    {
        return $this->getData(self::INVENTORY_ITEMS_KEY);
    }

    /**
     * SetSku
     *
     * @param string|null $sku
     *
     * @return InventoryItemInterface
     */
    public function setSku(?string $sku = null): InventoryItemInterface
    {
        $this->setData(self::SKU_KEY, $sku);

        return $this;
    }

    /**
     * SetInventoryItems
     *
     * @param InventoryItemItemInterface[]|null $inventoryItems
     *
     * @return InventoryItemInterface
     */
    public function setInventoryItems(?array $inventoryItems = null): InventoryItemInterface
    {
        $this->setData(self::INVENTORY_ITEMS_KEY, $inventoryItems);

        return $this;
    }
}

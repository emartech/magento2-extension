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
     * @return string
     */
    public function getSku(): string
    {
        return (string) $this->getData(self::SKU_KEY);
    }

    /**
     * GetInventoryItems
     *
     * @return InventoryItemItemInterface[]
     */
    public function getInventoryItems(): array
    {
        return $this->getData(self::INVENTORY_ITEMS_KEY);
    }

    /**
     * SetSku
     *
     * @param string $sku
     *
     * @return InventoryItemInterface
     */
    public function setSku(string $sku): InventoryItemInterface
    {
        $this->setData(self::SKU_KEY, $sku);

        return $this;
    }

    /**
     * SetInventoryItems
     *
     * @param InventoryItemItemInterface[] $inventoryItems
     *
     * @return InventoryItemInterface
     */
    public function setInventoryItems(array $inventoryItems): InventoryItemInterface
    {
        $this->setData(self::INVENTORY_ITEMS_KEY, $inventoryItems);

        return $this;
    }
}

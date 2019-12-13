<?php

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\InventoryItemInterface;
use Emartech\Emarsys\Api\Data\InventoryItemItemInterface;
use Magento\Framework\DataObject;

class InventoryItem extends DataObject implements InventoryItemInterface
{
    /**
     * @return string
     */
    public function getSku()
    {
        return $this->getData(self::SKU_KEY);
    }

    /**
     * @return InventoryItemItemInterface[]
     */
    public function getInventoryItems()
    {
        return $this->getData(self::INVENTORY_ITEMS_KEY);
    }

    /**
     * @param string $sku
     *
     * @return $this
     */
    public function setSku($sku)
    {
        $this->setData(self::SKU_KEY, $sku);

        return $this;
    }

    /**
     * @param InventoryItemItemInterface[] $inventoryItems
     *
     * @return $this
     */
    public function setInventoryItems($inventoryItems)
    {
        $this->setData(self::INVENTORY_ITEMS_KEY, $inventoryItems);

        return $this;
    }
}

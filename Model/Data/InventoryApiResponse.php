<?php
/**
 * Copyright ©2019 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\InventoryApiResponseInterface;
use Emartech\Emarsys\Api\Data\InventoryItemInterface;
use Magento\Framework\DataObject;

class InventoryApiResponse extends DataObject implements InventoryApiResponseInterface
{
    /**
     * @param InventoryItemInterface[] $items
     *
     * @return $this
     */
    public function setItems(array $items)
    {
        $this->setData(self::ITEMS_KEY, $items);

        return $this;
    }

    /**
     * @return InventoryItemInterface[]
     */
    public function getItems()
    {
        return $this->getData(self::ITEMS_KEY);
    }
}

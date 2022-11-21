<?php

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\OrderInterface;
use Emartech\Emarsys\Api\Data\OrdersApiResponseInterface;

class OrdersApiResponse extends ListApiResponseBase implements OrdersApiResponseInterface
{
    /**
     * SetItems
     *
     * @param OrderInterface[] $items
     *
     * @return OrdersApiResponseInterface
     */
    public function setItems(array $items): OrdersApiResponseInterface
    {
        $this->setData(self::ITEMS_KEY, $items);

        return $this;
    }

    /**
     * GetItems
     *
     * @return OrderInterface[]
     */
    public function getItems(): array
    {
        return $this->getData(self::ITEMS_KEY);
    }
}

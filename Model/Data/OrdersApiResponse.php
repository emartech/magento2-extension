<?php

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\OrdersApiResponseInterface;
use Emartech\Emarsys\Api\Data\OrderInterface;

/**
 * Class OrdersApiResponse
 * @package Emartech\Emarsys\Model\Data
 */
class OrdersApiResponse extends ListApiResponseBase implements OrdersApiResponseInterface
{
    /**
     * @param OrderInterface[] $items
     *
     * @return $this
     */
    public function setItems(array $items)
    {
        $this->setData(self::ITEMS_KEY, $items);

        return $this;
    }

    /**
     * @return OrderInterface[]
     */
    public function getItems()
    {
        return $this->getData(self::ITEMS_KEY);
    }
}

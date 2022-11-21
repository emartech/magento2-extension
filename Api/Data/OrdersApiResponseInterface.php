<?php

namespace Emartech\Emarsys\Api\Data;

interface OrdersApiResponseInterface extends ListApiResponseBaseInterface
{
    public const ITEMS_KEY = 'items';

    /**
     * GetItems
     *
     * @return \Emartech\Emarsys\Api\Data\OrderInterface[]
     */
    public function getItems(): array;

    /**
     * SetItems
     *
     * @param \Emartech\Emarsys\Api\Data\OrderInterface[] $items
     *
     * @return \Emartech\Emarsys\Api\Data\OrdersApiResponseInterface
     */
    public function setItems(array $items): OrdersApiResponseInterface;
}

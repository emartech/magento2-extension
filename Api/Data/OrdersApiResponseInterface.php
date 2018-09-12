<?php

namespace Emartech\Emarsys\Api\Data;

/**
 * Interface OrdersApiResponseInterface
 * @package Emartech\Emarsys\Api\Data
 */
interface OrdersApiResponseInterface extends ListApiResponseBaseInterface
{
    const ITEMS_KEY = 'items';

    /**
     * @return \Magento\Sales\Api\Data\OrderInterface[]
     */
    public function getItems(): array;

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface[] $items
     *
     * @return $this
     */
    public function setItems(array $items): OrdersApiResponseInterface;
}

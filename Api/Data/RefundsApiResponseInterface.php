<?php

namespace Emartech\Emarsys\Api\Data;

interface RefundsApiResponseInterface extends ListApiResponseBaseInterface
{
    const ITEMS_KEY = 'items';

    /**
     * @return \Emartech\Emarsys\Api\Data\RefundInterface[]
     */
    public function getItems();

    /**
     * @param \Emartech\Emarsys\Api\Data\RefundInterface[] $items
     *
     * @return $this
     */
    public function setItems(array $items);
}

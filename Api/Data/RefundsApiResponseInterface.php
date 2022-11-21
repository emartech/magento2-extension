<?php

namespace Emartech\Emarsys\Api\Data;

interface RefundsApiResponseInterface extends ListApiResponseBaseInterface
{
    public const ITEMS_KEY = 'items';

    /**
     * GetItems
     *
     * @return \Emartech\Emarsys\Api\Data\RefundInterface[]
     */
    public function getItems(): array;

    /**
     * SetItems
     *
     * @param \Emartech\Emarsys\Api\Data\RefundInterface[] $items
     *
     * @return \Emartech\Emarsys\Api\Data\RefundsApiResponseInterface
     */
    public function setItems(array $items): RefundsApiResponseInterface;
}

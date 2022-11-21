<?php

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\RefundInterface;
use Emartech\Emarsys\Api\Data\RefundsApiResponseInterface;

class RefundsApiResponse extends ListApiResponseBase implements RefundsApiResponseInterface
{
    /**
     * SetItems
     *
     * @param RefundInterface[] $items
     *
     * @return $this
     */
    public function setItems(array $items): RefundsApiResponseInterface
    {
        $this->setData(self::ITEMS_KEY, $items);

        return $this;
    }

    /**
     * GetItems
     *
     * @return RefundInterface[]
     */
    public function getItems(): array
    {
        return $this->getData(self::ITEMS_KEY);
    }
}

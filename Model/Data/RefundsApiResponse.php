<?php

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\RefundsApiResponseInterface;
use Emartech\Emarsys\Api\Data\RefundInterface;

class RefundsApiResponse extends ListApiResponseBase implements RefundsApiResponseInterface
{
    /**
     * @param RefundInterface[] $items
     *
     * @return $this
     */
    public function setItems(array $items)
    {
        $this->setData(self::ITEMS_KEY, $items);

        return $this;
    }

    /**
     * @return RefundInterface[]
     */
    public function getItems()
    {
        return $this->getData(self::ITEMS_KEY);
    }
}

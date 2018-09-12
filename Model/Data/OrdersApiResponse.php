<?php

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\OrdersApiResponseInterface;

/**
 * Class OrdersApiResponse
 * @package Emartech\Emarsys\Model\Data
 */
class OrdersApiResponse extends ListApiResponseBase implements OrdersApiResponseInterface
{
    /**
     * @param array $items
     *
     * @return $this
     */
    public function setItems(array $items): OrdersApiResponseInterface
    {
        $this->setData(self::ITEMS_KEY, $items);

        return $this;
    }

    /**
     * @return \Magento\Sales\Api\Data\OrderInterface[]
     */
    public function getItems(): array
    {
        return $this->getData(self::ITEMS_KEY);
    }
}

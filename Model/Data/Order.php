<?php

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\OrderInterface;
use Magento\Sales\Model\Order as OriginalOrderModel;

class Order extends OriginalOrderModel implements OrderInterface
{

    /**
     * GetId
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->getEntityId();
    }

    /**
     * SetId
     *
     * @param int $id
     *
     * @return OrderInterface
     */
    public function setId($id): OrderInterface
    {
        $this->setEntityId($id);

        return $this;
    }
}

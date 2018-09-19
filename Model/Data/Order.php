<?php

namespace Emartech\Emarsys\Model\Data;

use Magento\Sales\Model\Order as OriginalOrderModel;

use Emartech\Emarsys\Api\Data\OrderInterface;

/**
 * Class Order
 * @package Emartech\Emarsys\Model\Data
 */
class Order extends OriginalOrderModel implements OrderInterface
{

    /**
     * @return int
     */
    public function getId()
    {
        return $this->getEntityId();
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->setEntityId($id);

        return $this;
    }
}
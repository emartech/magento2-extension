<?php

namespace Emartech\Emarsys\Model\Data;

use Magento\Sales\Model\Order\Creditmemo;

use Emartech\Emarsys\Api\Data\RefundInterface;

class Refund extends Creditmemo implements RefundInterface
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

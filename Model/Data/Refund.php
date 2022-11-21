<?php

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\RefundInterface;
use Magento\Sales\Model\Order\Creditmemo;

class Refund extends Creditmemo implements RefundInterface
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
     * @return $this
     */
    public function setId($id): RefundInterface
    {
        $this->setEntityId($id);

        return $this;
    }
}

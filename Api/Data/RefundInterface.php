<?php

namespace Emartech\Emarsys\Api\Data;

use Magento\Sales\Api\Data\CreditmemoInterface as OriginalRefundInterface;

interface RefundInterface extends OriginalRefundInterface
{
    public const ID_KEY = 'id';

    /**
     * GetId
     *
     * @return int
     */
    public function getId(): int;

    /**
     * SetId
     *
     * @param int $id
     *
     * @return \Emartech\Emarsys\Api\Data\RefundInterface
     */
    public function setId(int $id): RefundInterface;
}

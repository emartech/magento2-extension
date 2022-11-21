<?php

namespace Emartech\Emarsys\Api\Data;

use Magento\Sales\Api\Data\OrderInterface as OriginalOrderInterface;

interface OrderInterface extends OriginalOrderInterface
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
     * @return \Emartech\Emarsys\Api\Data\OrderInterface
     */
    public function setId($id): OrderInterface;
}

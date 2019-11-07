<?php

namespace Emartech\Emarsys\Api\Data;

use Magento\Sales\Api\Data\OrderInterface as OriginalOrderInterface;

interface OrderInterface extends OriginalOrderInterface
{
    const ID_KEY = 'id';

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id);
}

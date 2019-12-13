<?php
/**
 * Copyright ©2019 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Api\Data;

/**
 * Interface InventoryItemItemInterface
 */
interface InventoryItemItemInterface
{
    const SOURCE_CODE_KEY = 'source_code';
    const QUANTITY_KEY    = 'quantity';
    const STATUS_KEY      = 'status';

    /**
     * @return string
     */
    public function getSourceCode();

    /**
     * @param string $sourceCode
     *
     * @return $this
     */
    public function setSourceCode($sourceCode);

    /**
     * @return float
     */
    public function getQuantity();

    /**
     * @param float $quantity
     *
     * @return $this
     */
    public function setQuantity($quantity);

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param int $status
     *
     * @return $this
     */
    public function setStatus($status);
}

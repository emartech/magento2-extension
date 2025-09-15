<?php
/**
 * Copyright ©2019 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Api\Data;

interface InventoryItemItemInterface
{
    public const SOURCE_CODE_KEY = 'source_code';
    public const QUANTITY_KEY    = 'quantity';
    public const IS_IN_STOCK_KEY = 'is_in_stock';

    /**
     * GetSourceCode
     *
     * @return string|null
     */
    public function getSourceCode(): ?string;

    /**
     * SetSourceCode
     *
     * @param string|null $sourceCode
     *
     * @return \Emartech\Emarsys\Api\Data\InventoryItemItemInterface
     */
    public function setSourceCode(?string $sourceCode = null): InventoryItemItemInterface;

    /**
     * GetQuantity
     *
     * @return float|null
     */
    public function getQuantity(): ?float;

    /**
     * SetQuantity
     *
     * @param float|null $quantity
     *
     * @return \Emartech\Emarsys\Api\Data\InventoryItemItemInterface
     */
    public function setQuantity(?float $quantity = null): InventoryItemItemInterface;

    /**
     * GetIsInStock
     *
     * @return bool|null
     */
    public function getIsInStock(): ?bool;

    /**
     * SetIsInStock
     *
     * @param bool|null $isInStock
     *
     * @return \Emartech\Emarsys\Api\Data\InventoryItemItemInterface
     */
    public function setIsInStock(?bool $isInStock = null): InventoryItemItemInterface;
}

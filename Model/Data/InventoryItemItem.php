<?php
/**
 * Copyright ©2019 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\InventoryItemItemInterface;
use Magento\Framework\DataObject;

class InventoryItemItem extends DataObject implements InventoryItemItemInterface
{
    /**
     * GetQuantity
     *
     * @return float
     */
    public function getQuantity(): float
    {
        return (float) $this->getData(self::QUANTITY_KEY);
    }

    /**
     * GetSourceCode
     *
     * @return string
     */
    public function getSourceCode(): string
    {
        return (string) $this->getData(self::SOURCE_CODE_KEY);
    }

    /**
     * SetQuantity
     *
     * @param float $quantity
     *
     * @return InventoryItemItemInterface
     */
    public function setQuantity(float $quantity): InventoryItemItemInterface
    {
        $this->setData(self::QUANTITY_KEY, $quantity);

        return $this;
    }

    /**
     * SetSourceCode
     *
     * @param string $sourceCode
     *
     * @return InventoryItemItemInterface
     */
    public function setSourceCode(string $sourceCode): InventoryItemItemInterface
    {
        $this->setData(self::SOURCE_CODE_KEY, $sourceCode);

        return $this;
    }

    /**
     * GetIsInStock
     *
     * @return bool
     */
    public function getIsInStock(): bool
    {
        return (bool) $this->getData(self::IS_IN_STOCK_KEY);
    }

    /**
     * SetIsInStock
     *
     * @param bool $isInStock
     *
     * @return InventoryItemItemInterface
     */
    public function setIsInStock(bool $isInStock): InventoryItemItemInterface
    {
        $this->setData(self::IS_IN_STOCK_KEY, $isInStock);

        return $this;
    }
}

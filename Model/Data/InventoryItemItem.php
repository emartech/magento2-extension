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
     * @return float|null
     */
    public function getQuantity(): ?float
    {
        return $this->getData(self::QUANTITY_KEY);
    }

    /**
     * GetSourceCode
     *
     * @return string|null
     */
    public function getSourceCode(): ?string
    {
        return $this->getData(self::SOURCE_CODE_KEY);
    }

    /**
     * SetQuantity
     *
     * @param float|null $quantity
     *
     * @return InventoryItemItemInterface
     */
    public function setQuantity(?float $quantity = null): InventoryItemItemInterface
    {
        $this->setData(self::QUANTITY_KEY, $quantity);

        return $this;
    }

    /**
     * SetSourceCode
     *
     * @param string|null $sourceCode
     *
     * @return InventoryItemItemInterface
     */
    public function setSourceCode(?string $sourceCode = null): InventoryItemItemInterface
    {
        $this->setData(self::SOURCE_CODE_KEY, $sourceCode);

        return $this;
    }

    /**
     * GetIsInStock
     *
     * @return bool|null
     */
    public function getIsInStock(): ?bool
    {
        return (bool) $this->getData(self::IS_IN_STOCK_KEY);
    }

    /**
     * SetIsInStock
     *
     * @param bool|null $isInStock
     *
     * @return InventoryItemItemInterface
     */
    public function setIsInStock(?bool $isInStock = null): InventoryItemItemInterface
    {
        $this->setData(self::IS_IN_STOCK_KEY, $isInStock);

        return $this;
    }
}

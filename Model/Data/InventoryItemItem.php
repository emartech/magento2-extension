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
     * @return float
     */
    public function getQuantity()
    {
        return $this->getData(self::QUANTITY_KEY);
    }

    /**
     * @return string
     */
    public function getSourceCode()
    {
        return $this->getData(self::SOURCE_CODE_KEY);
    }

    /**
     * @param float $quantity
     *
     * @return $this
     */
    public function setQuantity($quantity)
    {
        $this->setData(self::QUANTITY_KEY, $quantity);

        return $this;
    }

    /**
     * @param string $sourceCode
     *
     * @return $this
     */
    public function setSourceCode($sourceCode)
    {
        $this->setData(self::SOURCE_CODE_KEY, $sourceCode);

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsInStock()
    {
        return (bool) $this->getData(self::IS_IN_STOCK_KEY);
    }

    /**
     * @param int $isInStock
     *
     * @return $this
     */
    public function setIsInStock($isInStock)
    {
        $this->setData(self::IS_IN_STOCK_KEY, $isInStock);

        return $this;
    }
}

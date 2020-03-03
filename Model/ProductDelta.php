<?php
/**
 * Copyright ©2020 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Model;

use Emartech\Emarsys\Api\Data\ProductDeltaInterface;
use Emartech\Emarsys\Model\ResourceModel\ProductDelta as ProductDeltaResourceModel;
use Magento\Framework\Model\AbstractModel;

class ProductDelta extends AbstractModel implements ProductDeltaInterface
{
    protected function _construct()
    {
        $this->_init(ProductDeltaResourceModel::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(self::PRODUCT_DELTA_ID_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function getSku()
    {
        return $this->getData(self::PRODUCT_SKU_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityId()
    {
        return $this->getData(self::PRODUCT_ENTITY_ID_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function getRowId()
    {
        return $this->getData(self::PRODUCT_ROW_ID_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($productDeltaId)
    {
        $this->setData(self::PRODUCT_DELTA_ID_KEY, $productDeltaId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setSku($sku)
    {
        $this->setData(self::PRODUCT_SKU_KEY, $sku);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setEntityId($entityId)
    {
        $this->setData(self::PRODUCT_ENTITY_ID_KEY, $entityId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setRowId($rowId)
    {
        $this->setData(self::PRODUCT_ROW_ID_KEY, $rowId);

        return $this;
    }
}

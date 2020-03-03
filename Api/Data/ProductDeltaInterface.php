<?php
/**
 * Copyright ©2020 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Api\Data;

interface ProductDeltaInterface
{
    const PRODUCT_DELTA_ID_KEY  = 'product_delta_id';
    const PRODUCT_SKU_KEY       = 'sku';
    const PRODUCT_ENTITY_ID_KEY = 'entity_id';
    const PRODUCT_ROW_ID_KEY    = 'row_id';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getSku();

    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @return int|null
     */
    public function getRowId();

    /**
     * @param int $productDeltaId
     *
     * @return $this
     */
    public function setId($productDeltaId);

    /**
     * @param string $sku
     *
     * @return $this
     */
    public function setSku($sku);

    /**
     * @param int $entityId
     *
     * @return $this
     */
    public function setEntityId($entityId);

    /**
     * @param int $rowId
     *
     * @return $this
     */
    public function setRowId($rowId);
}

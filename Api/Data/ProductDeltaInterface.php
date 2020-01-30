<?php
/**
 * Copyright ©2020 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Api\Data;


interface ProductDeltaInterface
{
    const PRODUCT_DELTA_ID_KEY = 'product_delta_id';
    const PRODUCT_SKU_KEY      = 'sku';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getSku();

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
}
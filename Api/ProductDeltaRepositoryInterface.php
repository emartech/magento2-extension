<?php
/**
 * Copyright ©2020 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Api;

use Emartech\Emarsys\Api\Data\ProductDeltaInterface;

interface ProductDeltaRepositoryInterface
{
    /**
     * @param $id
     *
     * @return \Emartech\Emarsys\Api\Data\ProductDeltaInterface
     */
    public function get($id);

    /**
     * @param \Emartech\Emarsys\Api\Data\ProductDeltaInterface $productDelta
     *
     * @return \Emartech\Emarsys\Api\Data\ProductDeltaInterface
     */
    public function save(ProductDeltaInterface $productDelta);

    /**
     * @param string sinceId
     *
     * @return bool
     */
    public function isSinceIdIsHigherThanAutoIncrement($sinceId);
}
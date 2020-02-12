<?php
/**
 * Copyright ©2020 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Api\Data;

interface ProductDeltasApiResponseInterface extends ProductsApiResponseInterface
{
    const MAX_ID_KEY = 'max_id';

    /**
     * @return int
     */
    public function getMaxId();

    /**
     * @param int $maxId
     *
     * @return $this
     */
    public function setMaxId($maxId);
}

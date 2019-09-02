<?php
/**
 * Copyright ©2019 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Api;

use Emartech\Emarsys\Api\Data\AttributesApiResponseInterface;

/**
 * Interface AttributesApiInterface
 * @package Emartech\Emarsys\Api
 */
interface AttributesApiInterface
{
    const TYPE_CUSTOMER         = 'customer';
    const TYPE_CUSTOMER_ADDRESS = 'customer_address';
    const TYPE_PRODUCT          = 'product';
    const TYPE_CATEGORY         = 'category';

    const CUSTOMER_ENTITY_TYPE_ID         = 1;
    const CUSTOMER_ADDRESS_ENTITY_TYPE_ID = 2;
    const CATEGORY_ENTITY_TYPE_ID         = 3;
    const PRODUCT_ENTITY_TYPE_ID          = 4;

    /**
     * @param string $type
     *
     * @return \Emartech\Emarsys\Api\Data\AttributesApiResponseInterface
     */
    public function get($type);
}

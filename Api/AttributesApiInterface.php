<?php
/**
 * Copyright ©2019 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Api;

use Emartech\Emarsys\Api\Data\AttributesApiResponseInterface;

interface AttributesApiInterface
{
    public const TYPE_CUSTOMER         = 'customer';
    public const TYPE_CUSTOMER_ADDRESS = 'customer_address';
    public const TYPE_PRODUCT          = 'product';
    public const TYPE_CATEGORY         = 'category';

    public const TYPES = [
        self::TYPE_CUSTOMER,
        self::TYPE_CUSTOMER_ADDRESS,
        self::TYPE_PRODUCT,
        self::TYPE_CATEGORY,
    ];

    public const CUSTOMER_ENTITY_TYPE_ID         = 1;
    public const CUSTOMER_ADDRESS_ENTITY_TYPE_ID = 2;
    public const CATEGORY_ENTITY_TYPE_ID         = 3;
    public const PRODUCT_ENTITY_TYPE_ID          = 4;

    /**
     * Get
     *
     * @param string $type
     *
     * @return \Emartech\Emarsys\Api\Data\AttributesApiResponseInterface
     */
    public function get(string $type): AttributesApiResponseInterface;
}

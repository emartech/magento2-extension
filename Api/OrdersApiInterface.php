<?php

namespace Emartech\Emarsys\Api;

use Emartech\Emarsys\Api\Data\OrdersApiResponseInterface;

interface OrdersApiInterface
{
    /**
     * Get
     *
     * @param int         $page
     * @param int         $pageSize
     * @param int         $sinceId
     * @param string|null $storeId
     *
     * @return \Emartech\Emarsys\Api\Data\OrdersApiResponseInterface
     */
    public function get(int $page, int $pageSize, int $sinceId = 0, string $storeId = null): OrdersApiResponseInterface;
}

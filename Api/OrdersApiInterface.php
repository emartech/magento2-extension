<?php

namespace Emartech\Emarsys\Api;

use Emartech\Emarsys\Api\Data\OrdersApiResponseInterface;

/**
 * Interface OrderApiInterface
 * @package Emartech\Emarsys\Api
 */
interface OrdersApiInterface
{
    /**
     * @param int         $page
     * @param int         $pageSize
     * @param int         $sinceId
     * @param string|null $storeId
     *
     * @return \Emartech\Emarsys\Api\Data\OrdersApiResponseInterface
     */
    public function get($page, $pageSize, $sinceId = 0, $storeId = null);
}

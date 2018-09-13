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
     * @param int     $page
     * @param int     $pageSize
     * @param string|null $storeId
     *
     * @return \Emartech\Emarsys\Api\Data\OrdersApiResponseInterface
     */
    public function get($page, $pageSize, $storeId = null);
}

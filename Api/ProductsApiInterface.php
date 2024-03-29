<?php

namespace Emartech\Emarsys\Api;

use Emartech\Emarsys\Api\Data\ProductsApiResponseInterface;

interface ProductsApiInterface
{
    /**
     * Get
     *
     * @param int    $page
     * @param int    $pageSize
     * @param string $storeId
     *
     * @return \Emartech\Emarsys\Api\Data\ProductsApiResponseInterface
     */
    public function get(int $page, int $pageSize, string $storeId): ProductsApiResponseInterface;
}

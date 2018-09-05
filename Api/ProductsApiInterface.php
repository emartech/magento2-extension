<?php


namespace Emartech\Emarsys\Api;

/**
 * Interface ProductsApiInterface
 * @package Emartech\Emarsys\Api
 */
interface ProductsApiInterface
{
    /**
     * @param int    $page
     * @param int    $pageSize
     * @param string $storeId
     *
     * @return \Emartech\Emarsys\Api\Data\ProductsApiResponseInterface
     */
    public function get($page, $pageSize, $storeId);
}

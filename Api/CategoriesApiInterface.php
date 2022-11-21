<?php

namespace Emartech\Emarsys\Api;

use Emartech\Emarsys\Api\Data\CategoriesApiResponseInterface;

interface CategoriesApiInterface
{
    /**
     * Get
     *
     * @param int    $page
     * @param int    $pageSize
     * @param string $storeId
     *
     * @return \Emartech\Emarsys\Api\Data\CategoriesApiResponseInterface
     */
    public function get(int $page, int $pageSize, string $storeId): CategoriesApiResponseInterface;
}

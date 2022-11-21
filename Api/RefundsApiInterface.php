<?php

namespace Emartech\Emarsys\Api;

use Emartech\Emarsys\Api\Data\RefundsApiResponseInterface;

interface RefundsApiInterface
{
    /**
     * Get
     *
     * @param int         $page
     * @param int         $pageSize
     * @param int         $sinceId
     * @param string|null $storeId
     *
     * @return \Emartech\Emarsys\Api\Data\RefundsApiResponseInterface
     */
    public function get(
        int $page,
        int $pageSize,
        int $sinceId = 0,
        string $storeId = null
    ): RefundsApiResponseInterface;
}

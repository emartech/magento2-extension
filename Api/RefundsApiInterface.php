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
     * @param string|null $lastUpdatedFrom
     * @param string|null $lastUpdatedTo
     *
     * @return \Emartech\Emarsys\Api\Data\RefundsApiResponseInterface
     */
    public function get(
        int $page,
        int $pageSize,
        int $sinceId = 0,
        ?string $storeId = null,
        ?string $lastUpdatedFrom = null,
        ?string $lastUpdatedTo = null
    ): RefundsApiResponseInterface;
}

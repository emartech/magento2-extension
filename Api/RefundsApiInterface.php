<?php

namespace Emartech\Emarsys\Api;

use Emartech\Emarsys\Api\Data\RefundsApiResponseInterface;

interface RefundsApiInterface
{
    /**
     * @param int         $page
     * @param int         $pageSize
     * @param int         $sinceId
     * @param string|null $storeId
     *
     * @return \Emartech\Emarsys\Api\Data\RefundsApiResponseInterface
     */
    public function get($page, $pageSize, $sinceId = 0, $storeId = null);
}

<?php

namespace Emartech\Emarsys\Api;

use Emartech\Emarsys\Api\Data\CustomersApiResponseInterface;

interface CustomersApiInterface
{
    /**
     * @param int         $page
     * @param int         $pageSize
     * @param string|null $websiteId
     * @param string|null $storeId
     * @param bool|null   $onlyReg
     *
     * @return \Emartech\Emarsys\Api\Data\CustomersApiResponseInterface
     */
    public function get($page, $pageSize, $websiteId = null, $storeId = null, $onlyReg = null);
}

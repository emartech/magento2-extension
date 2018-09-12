<?php

namespace Emartech\Emarsys\Api;

use Emartech\Emarsys\Api\Data\CustomersApiResponseInterface;

/**
 * Interface CustomersApiInterface
 * @package Emartech\Emarsys\Api
 */
interface CustomersApiInterface
{
    /**
     * @param int         $page
     * @param int         $pageSize
     * @param string|null $websiteId
     * @param string|null $storeId
     *
     * @return \Emartech\Emarsys\Api\Data\CustomersApiResponseInterface
     */
    public function get($page, $pageSize, $websiteId = null, $storeId = null): CustomersApiResponseInterface;
}

<?php

namespace Emartech\Emarsys\Api;

/**
 * Interface CustomersApiInterface
 * @package Emartech\Emarsys\Api
 */
interface CustomersApiInterface
{
    /**
     * @param int   $page
     * @param int   $pageSize
     * @param mixed $websiteId
     * @param mixed $storeId
     *
     * @return \Emartech\Emarsys\Api\Data\CustomersApiResponseInterface
     */
    public function get($page, $pageSize, $websiteId = null, $storeId = null);
}

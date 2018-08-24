<?php

namespace Emartech\Emarsys\Api;

/**
 * Interface CustomersApiInterface
 * @package Emartech\Emarsys\Api
 */
interface CustomersApiInterface
{
    /**
     * @param int $page
     * @param int $pageSize
     * @param int $websiteId
     *
     * @return \Emartech\Emarsys\Api\Data\CustomersApiResponseInterface
     */
    public function get($page, $pageSize, $websiteId = null);
}

<?php

namespace Emartech\Emarsys\Api;

use Emartech\Emarsys\Api\Data\CustomersApiResponseInterface;

interface CustomersApiInterface
{
    /**
     * Get
     *
     * @param int         $page
     * @param int         $pageSize
     * @param string|null $websiteId
     * @param string|null $storeId
     * @param bool|null   $onlyReg
     *
     * @return \Emartech\Emarsys\Api\Data\CustomersApiResponseInterface
     */
    public function get(
        int $page,
        int $pageSize,
        ?string $websiteId = null,
        ?string $storeId = null,
        ?bool $onlyReg = null
    ): CustomersApiResponseInterface;
}

<?php

namespace Emartech\Emarsys\Api;

use Emartech\Emarsys\Api\Data\StatusResponseInterface;
use Emartech\Emarsys\Api\Data\SubscriptionsApiResponseInterface;

interface SubscriptionsApiInterface
{
    /**
     * Get
     *
     * @param int         $page
     * @param int         $pageSize
     * @param bool        $subscribed
     * @param bool        $onlyGuest
     * @param string|null $websiteId
     * @param string|null $storeId
     *
     * @return \Emartech\Emarsys\Api\Data\SubscriptionsApiResponseInterface
     */
    public function get(
        int $page = 1,
        int $pageSize = 1000,
        ?bool $subscribed = null,
        bool $onlyGuest = false,
        ?string $websiteId = null,
        ?string $storeId = null
    ): SubscriptionsApiResponseInterface;

    /**
     * Update
     *
     * @param \Emartech\Emarsys\Api\Data\SubscriptionInterface[] $subscriptions
     *
     * @return \Emartech\Emarsys\Api\Data\StatusResponseInterface
     */
    public function update(array $subscriptions): StatusResponseInterface;
}

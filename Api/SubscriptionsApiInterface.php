<?php

namespace Emartech\Emarsys\Api;

/**
 * Interface SubscriptionsApiInterface
 * @package Emartech\Emarsys\Api
 */
interface SubscriptionsApiInterface
{
    /**
     * @param int   $page
     * @param int   $pageSize
     * @param mixed $subscribed
     * @param mixed $onlyGuest
     * @param mixed $websiteId
     * @param mixed $storeId
     *
     * @return \Emartech\Emarsys\Api\Data\SubscriptionsApiResponseInterface
     */
    public function get($page = 1, $pageSize = 1000, $subscribed = null, $onlyGuest = null, $websiteId = null, $storeId = null);

    /**
     * @param \Emartech\Emarsys\Api\Data\SubscriptionInterface[] $subscriptions
     *
     * @return \Emartech\Emarsys\Api\Data\StatusResponseInterface
     */
    public function update($subscriptions);
}

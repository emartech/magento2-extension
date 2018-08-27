<?php

namespace Emartech\Emarsys\Api\Data;

/**
 * Interface SubscriptionsApiResponseInterface
 * @package Emartech\Emarsys\Api\Data
 */
interface SubscriptionsApiResponseInterface extends CustomersApiResponseBaseInterface
{
    const SUBSCRIPTIONS_KEY = 'subscriptions';

    /**
     * @return \Emartech\Emarsys\Api\Data\SubscriptionInterface[]
     */
    public function getSubscriptions();

    /**
     * @param \Emartech\Emarsys\Api\Data\SubscriptionInterface[] $subscriptions
     *
     * @return $this
     */
    public function setSubscriptions(array $subscriptions);
}

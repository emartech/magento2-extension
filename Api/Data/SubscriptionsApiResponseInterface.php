<?php

namespace Emartech\Emarsys\Api\Data;

interface SubscriptionsApiResponseInterface extends ListApiResponseBaseInterface
{
    public const SUBSCRIPTIONS_KEY = 'subscriptions';

    /**
     * GetSubscriptions
     *
     * @return \Emartech\Emarsys\Api\Data\SubscriptionInterface[]
     */
    public function getSubscriptions(): array;

    /**
     * SetSubscriptions
     *
     * @param \Emartech\Emarsys\Api\Data\SubscriptionInterface[] $subscriptions
     *
     * @return \Emartech\Emarsys\Api\Data\SubscriptionsApiResponseInterface
     */
    public function setSubscriptions(array $subscriptions): SubscriptionsApiResponseInterface;
}

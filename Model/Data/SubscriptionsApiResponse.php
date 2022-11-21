<?php

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\SubscriptionInterface;
use Emartech\Emarsys\Api\Data\SubscriptionsApiResponseInterface;

class SubscriptionsApiResponse extends ListApiResponseBase implements SubscriptionsApiResponseInterface
{
    /**
     * GetSubscriptions
     *
     * @return SubscriptionInterface[]
     */
    public function getSubscriptions(): array
    {
        return $this->getData(self::SUBSCRIPTIONS_KEY);
    }

    /**
     * SetSubscriptions
     *
     * @param SubscriptionInterface[] $subscriptions
     *
     * @return SubscriptionsApiResponseInterface
     */
    public function setSubscriptions(array $subscriptions): SubscriptionsApiResponseInterface
    {
        $this->setData(self::SUBSCRIPTIONS_KEY, $subscriptions);

        return $this;
    }
}

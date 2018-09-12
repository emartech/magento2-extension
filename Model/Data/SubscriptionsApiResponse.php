<?php

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\SubscriptionsApiResponseInterface;

/**
 * Class SubscriptionsApiResponse
 * @package Emartech\Emarsys\Model\Data
 */
class SubscriptionsApiResponse extends ListApiResponseBase implements SubscriptionsApiResponseInterface
{
    /**
     * @return \Emartech\Emarsys\Api\Data\SubscriptionInterface[]
     */
    public function getSubscriptions(): array
    {
        return $this->getData(self::SUBSCRIPTIONS_KEY);
    }

    /**
     * @param \Emartech\Emarsys\Api\Data\SubscriptionInterface[] $subscriptions
     *
     * @return $this
     */
    public function setSubscriptions(array $subscriptions): SubscriptionsApiResponseInterface
    {
        $this->setData(self::SUBSCRIPTIONS_KEY, $subscriptions);
        return $this;
    }
}

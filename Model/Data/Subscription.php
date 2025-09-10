<?php

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\SubscriptionInterface;
use Magento\Framework\DataObject;

class Subscription extends DataObject implements SubscriptionInterface
{
    /**
     * GetCustomerId
     *
     * @return int|null
     */
    public function getCustomerId(): ?int
    {
        return $this->getData(self::CUSTOMER_ID_KEY);
    }

    /**
     * GetSubscriberId
     *
     * @return int|null
     */
    public function getSubscriberId(): ?int
    {
        return $this->getData(self::SUBSCRIBER_ID);
    }

    /**
     * GetSubscriberEmail
     *
     * @return string|null
     */
    public function getSubscriberEmail(): ?string
    {
        return $this->getData(self::SUBSCRIBER_EMAIL);
    }

    /**
     * GetSubscriberStatus
     *
     * @return string|null
     */
    public function getSubscriberStatus(): ?string
    {
        return $this->getData(self::SUBSCRIBER_STATUS);
    }

    /**
     * GetStoreId
     *
     * @return int|null
     */
    public function getStoreId(): ?int
    {
        return $this->getData(self::STORE_ID_KEY);
    }

    /**
     * GetWebsiteId
     *
     * @return int|null
     */
    public function getWebsiteId(): ?int
    {
        return $this->getData(self::WEBSITE_ID_KEY);
    }

    /**
     * SetCustomerId
     *
     * @param int|null $customerId
     *
     * @return SubscriptionInterface
     */
    public function setCustomerId(?int $customerId = null): SubscriptionInterface
    {
        $this->setData(self::CUSTOMER_ID_KEY, $customerId);

        return $this;
    }

    /**
     * SetSubscriberId
     *
     * @param int|null $subscriberId
     *
     * @return SubscriptionInterface
     */
    public function setSubscriberId(?int $subscriberId = null): SubscriptionInterface
    {
        $this->setData(self::SUBSCRIBER_ID, $subscriberId);

        return $this;
    }

    /**
     * SetSubscriberEmail
     *
     * @param string|null $subscriberEmail
     *
     * @return SubscriptionInterface
     */
    public function setSubscriberEmail(?string $subscriberEmail = null): SubscriptionInterface
    {
        $this->setData(self::SUBSCRIBER_EMAIL, $subscriberEmail);

        return $this;
    }

    /**
     * SetSubscriberStatus
     *
     * @param string|null $subscriberStatus
     *
     * @return SubscriptionInterface
     */
    public function setSubscriberStatus(?string $subscriberStatus = null): SubscriptionInterface
    {
        $this->setData(self::SUBSCRIBER_STATUS, $subscriberStatus);

        return $this;
    }

    /**
     * SetStoreId
     *
     * @param int|null $storeId
     *
     * @return SubscriptionInterface
     */
    public function setStoreId(?int $storeId = null): SubscriptionInterface
    {
        $this->setData(self::STORE_ID_KEY, $storeId);

        return $this;
    }

    /**
     * SetWebsiteId
     *
     * @param int|null $websiteId
     *
     * @return SubscriptionInterface
     */
    public function setWebsiteId(?int $websiteId = null): SubscriptionInterface
    {
        $this->setData(self::WEBSITE_ID_KEY, $websiteId);

        return $this;
    }
}

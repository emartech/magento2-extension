<?php

namespace Emartech\Emarsys\Model\Data;

use Magento\Framework\DataObject;

use Emartech\Emarsys\Api\Data\SubscriptionInterface;

/**
 * Class Subscription
 * @package Emartech\Emarsys\Model\Data
 */
class Subscription extends DataObject implements SubscriptionInterface
{
    /**
     * @return int
     */
    public function getCustomerId(): int
    {
        return $this->getData(self::CUSTOMER_ID_KEY);
    }

    /**
     * @return string
     */
    public function getSubscriberEmail(): string
    {
        return $this->getData(self::SUBSCRIBER_EMAIL);
    }

    /**
     * @return string
     */
    public function getSubscriberStatus(): string
    {
        return $this->getData(self::SUBSCRIBER_STATUS);
    }

    /**
     * @return int
     */
    public function getStoreId(): int
    {
        return $this->getData(self::STORE_ID_KEY);
    }

    /**
     * @return int
     */
    public function getWebsiteId(): int
    {
        return $this->getData(self::WEBSITE_ID_KEY);
    }

    /**
     * @param int $customerId
     *
     * @return $this
     */
    public function setCustomerId($customerId): SubscriptionInterface
    {
        $this->setData(self::CUSTOMER_ID_KEY, $customerId);
        return $this;
    }

    /**
     * @param string $subscriberEmail
     *
     * @return $this
     */
    public function setSubscriberEmail($subscriberEmail): SubscriptionInterface
    {
        $this->setData(self::SUBSCRIBER_EMAIL, $subscriberEmail);
        return $this;
    }

    /**
     * @param string $subscriberStatus
     *
     * @return $this
     */
    public function setSubscriberStatus($subscriberStatus): SubscriptionInterface
    {
        $this->setData(self::SUBSCRIBER_STATUS, $subscriberStatus);
        return $this;
    }

    /**
     * @param int $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId): SubscriptionInterface
    {
        $this->setData(self::STORE_ID_KEY, $storeId);
        return $this;
    }

    /**
     * @param int $websiteId
     *
     * @return $this
     */
    public function setWebsiteId($websiteId): SubscriptionInterface
    {
        $this->setData(self::WEBSITE_ID_KEY, $websiteId);
        return $this;
    }
}

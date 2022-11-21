<?php

namespace Emartech\Emarsys\Api\Data;

interface SubscriptionInterface
{
    public const CUSTOMER_ID_KEY   = 'customer_id';
    public const STORE_ID_KEY      = 'store_id';
    public const WEBSITE_ID_KEY    = 'website_id';
    public const SUBSCRIBER_ID     = 'subscriber_id';
    public const SUBSCRIBER_EMAIL  = 'subscriber_email';
    public const SUBSCRIBER_STATUS = 'subscriber_status';

    /**
     * GetCustomerId
     *
     * @return int|null
     */
    public function getCustomerId(): ?int;

    /**
     * SetCustomerId
     *
     * @param int|null $customerId
     *
     * @return \Emartech\Emarsys\Api\Data\SubscriptionInterface
     */
    public function setCustomerId(int $customerId = null): SubscriptionInterface;

    /**
     * GetSubscriberId
     *
     * @return int|null
     */
    public function getSubscriberId(): ?int;

    /**
     * SetSubscriberId
     *
     * @param int|null $subscriberId
     *
     * @return \Emartech\Emarsys\Api\Data\SubscriptionInterface
     */
    public function setSubscriberId(int $subscriberId = null): SubscriptionInterface;

    /**
     * GetWebsiteId
     *
     * @return int|null
     */
    public function getWebsiteId(): ?int;

    /**
     * SetWebsiteId
     *
     * @param int|null $websiteId
     *
     * @return \Emartech\Emarsys\Api\Data\SubscriptionInterface
     */
    public function setWebsiteId(int $websiteId = null): SubscriptionInterface;

    /**
     * GetStoreId
     *
     * @return int
     */
    public function getStoreId(): int;

    /**
     * SetStoreId
     *
     * @param int $storeId
     *
     * @return \Emartech\Emarsys\Api\Data\SubscriptionInterface
     */
    public function setStoreId(int $storeId): SubscriptionInterface;

    /**
     * GetSubscriberEmail
     *
     * @return string
     */
    public function getSubscriberEmail(): string;

    /**
     * SetSubscriberEmail
     *
     * @param string $subscriberEmail
     *
     * @return \Emartech\Emarsys\Api\Data\SubscriptionInterface
     */
    public function setSubscriberEmail(string $subscriberEmail): SubscriptionInterface;

    /**
     * GetSubscriberStatus
     *
     * @return string
     */
    public function getSubscriberStatus(): string;

    /**
     * SetSubscriberStatus
     *
     * @param string $subscriberStatus
     *
     * @return \Emartech\Emarsys\Api\Data\SubscriptionInterface
     */
    public function setSubscriberStatus(string $subscriberStatus): SubscriptionInterface;
}

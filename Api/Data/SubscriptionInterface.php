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
    public function setCustomerId(?int $customerId = null): SubscriptionInterface;

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
    public function setSubscriberId(?int $subscriberId = null): SubscriptionInterface;

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
    public function setWebsiteId(?int $websiteId = null): SubscriptionInterface;

    /**
     * GetStoreId
     *
     * @return int|null
     */
    public function getStoreId(): ?int;

    /**
     * SetStoreId
     *
     * @param int|null $storeId
     *
     * @return \Emartech\Emarsys\Api\Data\SubscriptionInterface
     */
    public function setStoreId(?int $storeId = null): SubscriptionInterface;

    /**
     * GetSubscriberEmail
     *
     * @return string|null
     */
    public function getSubscriberEmail(): ?string;

    /**
     * SetSubscriberEmail
     *
     * @param string|null $subscriberEmail
     *
     * @return \Emartech\Emarsys\Api\Data\SubscriptionInterface
     */
    public function setSubscriberEmail(?string $subscriberEmail = null): SubscriptionInterface;

    /**
     * GetSubscriberStatus
     *
     * @return string|null
     */
    public function getSubscriberStatus(): ?string;

    /**
     * SetSubscriberStatus
     *
     * @param string|null $subscriberStatus
     *
     * @return \Emartech\Emarsys\Api\Data\SubscriptionInterface
     */
    public function setSubscriberStatus(?string $subscriberStatus = null): SubscriptionInterface;
}

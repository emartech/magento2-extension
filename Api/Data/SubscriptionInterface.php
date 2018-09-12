<?php

namespace Emartech\Emarsys\Api\Data;

/**
 * Interface SubscriptionInterface
 * @package Emartech\Emarsys\Api\Data
 */
interface SubscriptionInterface
{
    const CUSTOMER_ID_KEY   = 'customer_id';
    const STORE_ID_KEY      = 'store_id';
    const WEBSITE_ID_KEY    = 'website_id';
    const SUBSCRIBER_EMAIL  = 'subscriber_email';
    const SUBSCRIBER_STATUS = 'subscriber_status';

    /**
     * @return int
     */
    public function getCustomerId(): int;

    /**
     * @param int $customerId
     *
     * @return $this
     */
    public function setCustomerId($customerId): SubscriptionInterface;

    /**
     * @return int
     */
    public function getWebsiteId(): int;

    /**
     * @param int $websiteId
     *
     * @return $this
     */
    public function setWebsiteId($websiteId): SubscriptionInterface;

    /**
     * @return int
     */
    public function getStoreId(): int;

    /**
     * @param int $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId): SubscriptionInterface;

    /**
     * @return string
     */
    public function getSubscriberEmail(): string;

    /**
     * @param string $subscriberEmail
     *
     * @return $this
     */
    public function setSubscriberEmail($subscriberEmail): SubscriptionInterface;

    /**
     * @return string
     */
    public function getSubscriberStatus(): string;

    /**
     * @param string $subscriberStatus
     *
     * @return $this
     */
    public function setSubscriberStatus($subscriberStatus): SubscriptionInterface;
}

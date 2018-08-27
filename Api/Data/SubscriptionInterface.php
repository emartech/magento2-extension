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
    public function getCustomerId();

    /**
     * @param int $customerId
     *
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * @return int
     */
    public function getWebsiteId();

    /**
     * @param int $websiteId
     *
     * @return $this
     */
    public function setWebsiteId($websiteId);

    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @param int $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId);

    /**
     * @return string
     */
    public function getSubscriberEmail();

    /**
     * @param string $subscriberEmail
     *
     * @return $this
     */
    public function setSubscriberEmail($subscriberEmail);

    /**
     * @return string
     */
    public function getSubscriberStatus();

    /**
     * @param string $subscriberStatus
     *
     * @return $this
     */
    public function setSubscriberStatus($subscriberStatus);
}

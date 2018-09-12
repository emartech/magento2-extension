<?php

namespace Emartech\Emarsys\Api\Data;

/**
 * Interface EventInterface
 * @package Emartech\Emarsys\Api\Data
 */
interface EventInterface
{
    const EVENT_ID_KEY   = 'event_id';
    const EVENT_TYPE_KEY = 'event_type';
    const EVENT_DATA_KEY = 'event_data';
    const CREATED_AT_KEY = 'created_at';
    const WEBSITE_ID_KEY = 'website_id';
    const STORE_ID_KEY   = 'store_id';

    /**
     * @return int
     */
    public function getEventId(): int;

    /**
     * @return string
     */
    public function getEventType(): string;

    /**
     * @return string
     */
    public function getEventData(): string;

    /**
     * @return string
     */
    public function getCreatedAt(): string;

    /**
     * @return int
     */
    public function getWebsiteId(): int;

    /**
     * @return int
     */
    public function getStoreId(): int;

    /**
     * @param int $eventId
     *
     * @return $this
     */
    public function setEventId($eventId): EventInterface;

    /**
     * @param string $eventType
     *
     * @return $this
     */
    public function setEventType($eventType): EventInterface;

    /**
     * @param string $eventData
     *
     * @return $this
     */
    public function setEventData($eventData): EventInterface;

    /**
     * @param string $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt): EventInterface;

    /**
     * @param int $websiteId
     *
     * @return $this
     */
    public function setWebsiteId($websiteId): EventInterface;

    /**
     * @param int $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId): EventInterface;
}

<?php

namespace Emartech\Emarsys\Api\Data;

interface EventInterface
{
    public const EVENT_ID_KEY   = 'event_id';
    public const EVENT_TYPE_KEY = 'event_type';
    public const EVENT_DATA_KEY = 'event_data';
    public const CREATED_AT_KEY = 'created_at';
    public const WEBSITE_ID_KEY = 'website_id';
    public const STORE_ID_KEY   = 'store_id';
    public const ENTITY_ID_KEY  = 'entity_id';

    /**
     * GetEventId
     *
     * @return int
     */
    public function getEventId(): int;

    /**
     * GetEventType
     *
     * @return string
     */
    public function getEventType(): string;

    /**
     * GetEventData
     *
     * @return string
     */
    public function getEventData(): string;

    /**
     * GetCreatedAt
     *
     * @return string
     */
    public function getCreatedAt(): string;

    /**
     * GetWebsiteId
     *
     * @return int
     */
    public function getWebsiteId(): int;

    /**
     * GetStoreId
     *
     * @return int
     */
    public function getStoreId(): int;

    /**
     * GetEntityId
     *
     * @return int
     */
    public function getEntityId(): int;

    /**
     * SetEventId
     *
     * @param int $eventId
     *
     * @return \Emartech\Emarsys\Api\Data\EventInterface
     */
    public function setEventId(int $eventId): EventInterface;

    /**
     * SetEventType
     *
     * @param string $eventType
     *
     * @return \Emartech\Emarsys\Api\Data\EventInterface
     */
    public function setEventType(string $eventType): EventInterface;

    /**
     * SetEventData
     *
     * @param string $eventData
     *
     * @return \Emartech\Emarsys\Api\Data\EventInterface
     */
    public function setEventData(string $eventData): EventInterface;

    /**
     * SetCreatedAt
     *
     * @param string $createdAt
     *
     * @return \Emartech\Emarsys\Api\Data\EventInterface
     */
    public function setCreatedAt(string $createdAt): EventInterface;

    /**
     * SetWebsiteId
     *
     * @param int $websiteId
     *
     * @return \Emartech\Emarsys\Api\Data\EventInterface
     */
    public function setWebsiteId(int $websiteId): EventInterface;

    /**
     * SetStoreId
     *
     * @param int $storeId
     *
     * @return \Emartech\Emarsys\Api\Data\EventInterface
     */
    public function setStoreId(int $storeId): EventInterface;

    /**
     * SetEntityId
     *
     * @param int $entityId
     *
     * @return \Emartech\Emarsys\Api\Data\EventInterface
     */
    public function setEntityId(int $entityId): EventInterface;
}

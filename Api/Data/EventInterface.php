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
     * @return string|null
     */
    public function getEventType(): ?string;

    /**
     * GetEventData
     *
     * @return string|null
     */
    public function getEventData(): ?string;

    /**
     * GetCreatedAt
     *
     * @return string|null
     */
    public function getCreatedAt(): ?string;

    /**
     * GetWebsiteId
     *
     * @return int|null
     */
    public function getWebsiteId(): ?int;

    /**
     * GetStoreId
     *
     * @return int|null
     */
    public function getStoreId(): ?int;

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
     * @param string|null $eventType
     *
     * @return \Emartech\Emarsys\Api\Data\EventInterface
     */
    public function setEventType(?string $eventType = null): EventInterface;

    /**
     * SetEventData
     *
     * @param string|null $eventData
     *
     * @return \Emartech\Emarsys\Api\Data\EventInterface
     */
    public function setEventData(?string $eventData = null): EventInterface;

    /**
     * SetCreatedAt
     *
     * @param string|null $createdAt
     *
     * @return \Emartech\Emarsys\Api\Data\EventInterface
     */
    public function setCreatedAt(?string $createdAt = null): EventInterface;

    /**
     * SetWebsiteId
     *
     * @param int|null $websiteId
     *
     * @return \Emartech\Emarsys\Api\Data\EventInterface
     */
    public function setWebsiteId(?int $websiteId = null): EventInterface;

    /**
     * SetStoreId
     *
     * @param int|null $storeId
     *
     * @return \Emartech\Emarsys\Api\Data\EventInterface
     */
    public function setStoreId(?int $storeId = null): EventInterface;

    /**
     * SetEntityId
     *
     * @param int $entityId
     *
     * @return \Emartech\Emarsys\Api\Data\EventInterface
     */
    public function setEntityId($entityId): EventInterface;
}

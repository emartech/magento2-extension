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

    /**
     * @return int
     */
    public function getEventId();

    /**
     * @return string
     */
    public function getEventType();

    /**
     * @return string
     */
    public function getEventData();

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param int $eventId
     *
     * @return $this
     */
    public function setEventId($eventId);

    /**
     * @param string $eventType
     *
     * @return $this
     */
    public function setEventType($eventType);

    /**
     * @param string $eventData
     *
     * @return $this
     */
    public function setEventData($eventData);

    /**
     * @param string $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt);
}

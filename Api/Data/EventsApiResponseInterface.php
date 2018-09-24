<?php

namespace Emartech\Emarsys\Api\Data;

/**
 * Interface EventsApiResponseInterface
 * @package Emartech\Emarsys\Api\Data
 */
interface EventsApiResponseInterface extends ListApiResponseBaseInterface
{
    const EVENTS_KEY = 'events';

    /**
     * @return \Emartech\Emarsys\Api\Data\EventInterface[]
     */
    public function getEvents();

    /**
     * @param \Emartech\Emarsys\Api\Data\EventInterface[] $events
     *
     * @return $this
     */
    public function setEvents(array $events);
}

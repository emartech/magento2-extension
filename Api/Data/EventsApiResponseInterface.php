<?php

namespace Emartech\Emarsys\Api\Data;

interface EventsApiResponseInterface extends ListApiResponseBaseInterface
{
    public const EVENTS_KEY = 'events';

    /**
     * GetEvents
     *
     * @return \Emartech\Emarsys\Api\Data\EventInterface[]
     */
    public function getEvents(): array;

    /**
     * SetEvents
     *
     * @param \Emartech\Emarsys\Api\Data\EventInterface[] $events
     *
     * @return \Emartech\Emarsys\Api\Data\EventsApiResponseInterface
     */
    public function setEvents(array $events): EventsApiResponseInterface;
}

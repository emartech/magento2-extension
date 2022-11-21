<?php

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\EventInterface;
use Emartech\Emarsys\Api\Data\EventsApiResponseInterface;

class EventsApiResponse extends ListApiResponseBase implements EventsApiResponseInterface
{
    /**
     * GetEvents
     *
     * @return EventInterface[]
     */
    public function getEvents(): array
    {
        return $this->getData(self::EVENTS_KEY);
    }

    /**
     * SetEvents
     *
     * @param EventInterface[] $events
     *
     * @return EventsApiResponseInterface
     */
    public function setEvents(array $events): EventsApiResponseInterface
    {
        $this->setData(self::EVENTS_KEY, $events);

        return $this;
    }
}

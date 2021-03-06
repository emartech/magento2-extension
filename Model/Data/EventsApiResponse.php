<?php

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\EventsApiResponseInterface;

class EventsApiResponse extends ListApiResponseBase implements EventsApiResponseInterface
{
    /**
     * @return \Emartech\Emarsys\Api\Data\EventInterface[]
     */
    public function getEvents()
    {
        return $this->getData(self::EVENTS_KEY);
    }

    /**
     * @param \Emartech\Emarsys\Api\Data\EventInterface[] $events
     *
     * @return $this
     */
    public function setEvents(array $events)
    {
        $this->setData(self::EVENTS_KEY, $events);
        return $this;
    }
}

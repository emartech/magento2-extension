<?php

namespace Emartech\Emarsys\Model\Api;

use Magento\Framework\Data\Collection as DataCollection;

use Emartech\Emarsys\Api\Data\EventsApiResponseInterfaceFactory;
use Emartech\Emarsys\Api\Data\EventsApiResponseInterface;
use Emartech\Emarsys\Api\EventsApiInterface;
use Emartech\Emarsys\Model\Event;
use Emartech\Emarsys\Model\ResourceModel\Event\CollectionFactory;
use Emartech\Emarsys\Model\ResourceModel\Event\Collection;

/**
 * Class EventsApi
 * @package Emartech\Emarsys\Model\Api
 */
class EventsApi implements EventsApiInterface
{
    /**
     * @var CollectionFactory
     */
    private $eventCollectionFactory;

    /**
     * @var EventsApiResponseInterfaceFactory
     */
    private $eventsApiResponseFactory;

    /**
     * @var Collection
     */
    private $eventCollection;

    /**
     * EventsApi constructor.
     *
     * @param CollectionFactory                 $eventCollectionFactory
     * @param EventsApiResponseInterfaceFactory $eventsApiResponseFactory
     */
    public function __construct(
        CollectionFactory $eventCollectionFactory,
        EventsApiResponseInterfaceFactory $eventsApiResponseFactory
    ) {
        $this->eventCollectionFactory = $eventCollectionFactory;
        $this->eventsApiResponseFactory = $eventsApiResponseFactory;
    }

    /**
     * @param int $sinceId
     * @param int $pageSize
     *
     * @return EventsApiResponseInterface
     */
    public function get($sinceId, $pageSize): EventsApiResponseInterface
    {
        $this
            ->initCollection()
            ->removeOldEvents($sinceId)
            ->initCollection()
            ->getEvents($sinceId)
            ->setOrder()
            ->setPageSize($pageSize);

        return $this->eventsApiResponseFactory->create()
            ->setCurrentPage($this->eventCollection->getCurPage())
            ->setLastPage($this->eventCollection->getLastPageNumber())
            ->setPageSize($this->eventCollection->getPageSize())
            ->setTotalCount($this->eventCollection->getSize())
            ->setEvents($this->handleEvents());
    }

    /**
     * @return array
     */
    private function handleEvents(): array
    {
        $eventsArray = [];

        /** @var Event $event */
        foreach ($this->eventCollection as $event) {
            $eventsArray[] = $event;
        }

        return $eventsArray;
    }

    /**
     * @return $this
     */
    private function initCollection(): EventsApi
    {
        $this->eventCollection = $this->eventCollectionFactory->create();

        return $this;
    }

    /**
     * @param int $sinceId
     *
     * @return $this
     */
    private function getEvents($sinceId): EventsApi
    {
        $this->eventCollection
            ->addFieldToFilter('event_id', ['gt' => $sinceId]);

        return $this;
    }

    /**
     * @return $this
     */
    private function setOrder(): EventsApi
    {
        $this->eventCollection
            ->setOrder('event_id', DataCollection::SORT_ORDER_ASC);

        return $this;
    }

    /**
     * @param int $pageSize
     *
     * @return $this
     */
    private function setPageSize($pageSize): EventsApi
    {
        $this->eventCollection
            ->setPageSize($pageSize);

        return $this;
    }

    /**
     * @param int $beforeId
     *
     * @return $this
     */
    private function removeOldEvents($beforeId): EventsApi
    {
        $oldEvents = $this->eventCollection
            ->addFieldToFilter('event_id', ['lteq' => $beforeId]);

        $oldEvents->walk('delete');

        return $this;
    }
}

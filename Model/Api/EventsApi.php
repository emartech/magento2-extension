<?php

namespace Emartech\Emarsys\Model\Api;

use Emartech\Emarsys\Api\Data\EventsApiResponseInterface;
use Emartech\Emarsys\Api\Data\EventsApiResponseInterfaceFactory;
use Emartech\Emarsys\Api\EventsApiInterface;
use Emartech\Emarsys\Model\Event;
use Emartech\Emarsys\Model\EventRepository;
use Emartech\Emarsys\Model\ResourceModel\Event\Collection;
use Emartech\Emarsys\Model\ResourceModel\Event\CollectionFactory;
use Magento\Framework\Data\Collection as DataCollection;
use Magento\Framework\Exception\LocalizedException;

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
     * @var EventRepository
     */
    private $eventRepository;

    /**
     * EventsApi constructor.
     *
     * @param EventRepository                   $eventRepository
     * @param CollectionFactory                 $eventCollectionFactory
     * @param EventsApiResponseInterfaceFactory $eventsApiResponseFactory
     */
    public function __construct(
        EventRepository $eventRepository,
        CollectionFactory $eventCollectionFactory,
        EventsApiResponseInterfaceFactory $eventsApiResponseFactory
    ) {
        $this->eventCollectionFactory = $eventCollectionFactory;
        $this->eventsApiResponseFactory = $eventsApiResponseFactory;
        $this->eventRepository = $eventRepository;
    }

    /**
     * Get
     *
     * @param int $sinceId
     * @param int $pageSize
     *
     * @return EventsApiResponseInterface
     * @throws LocalizedException
     */
    public function get(int $sinceId, int $pageSize): EventsApiResponseInterface
    {
        $this->validateSinceId($sinceId);

        $this
            ->removeOldEvents($sinceId)
            ->initCollection()
            ->getEvents($sinceId)
            ->setOrder()
            ->setPageSize($pageSize);

        return $this->eventsApiResponseFactory
            ->create()
            ->setCurrentPage($this->eventCollection->getCurPage())
            ->setLastPage($this->eventCollection->getLastPageNumber())
            ->setPageSize($this->eventCollection->getPageSize())
            ->setTotalCount($this->eventCollection->getSize())
            ->setEvents($this->handleEvents());
    }

    /**
     * HandleEvents
     *
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
     * InitCollection
     *
     * @return EventsApi
     */
    private function initCollection(): EventsApi
    {
        $this->eventCollection = $this->eventCollectionFactory->create();

        return $this;
    }

    /**
     * GetEvents
     *
     * @param int $sinceId
     *
     * @return EventsApi
     */
    private function getEvents(int $sinceId): EventsApi
    {
        $this->eventCollection
            ->addFieldToFilter('event_id', ['gt' => $sinceId]);

        return $this;
    }

    /**
     * SetOrder
     *
     * @return EventsApi
     */
    private function setOrder(): EventsApi
    {
        $this->eventCollection
            ->setOrder('event_id', DataCollection::SORT_ORDER_ASC);

        return $this;
    }

    /**
     * SetPageSize
     *
     * @param int $pageSize
     *
     * @return EventsApi
     */
    private function setPageSize(int $pageSize): EventsApi
    {
        $this->eventCollection
            ->setPageSize($pageSize);

        return $this;
    }

    /**
     * RemoveOldEvents
     *
     * @param int $beforeId
     *
     * @return EventsApi
     */
    private function removeOldEvents(int $beforeId): EventsApi
    {
        $this->eventRepository->deleteUntilSinceId($beforeId);

        return $this;
    }

    /**
     * ValidateSinceId
     *
     * @param int $sinceId
     *
     * @throws LocalizedException
     */
    private function validateSinceId(int $sinceId): void
    {
        if ($this->eventRepository->isSinceIdIsHigherThanAutoIncrement($sinceId)) {
            throw new LocalizedException(__('sinceId is higher than auto-increment'));
        }
    }
}

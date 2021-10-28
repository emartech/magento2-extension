<?php

namespace Emartech\Emarsys\Model\Api;

use Emartech\Emarsys\Model\EventRepository;
use Magento\Framework\Data\Collection as DataCollection;

use Emartech\Emarsys\Api\Data\EventsApiResponseInterfaceFactory;
use Emartech\Emarsys\Api\Data\EventsApiResponseInterface;
use Emartech\Emarsys\Api\EventsApiInterface;
use Emartech\Emarsys\Model\Event;
use Emartech\Emarsys\Model\ResourceModel\Event\CollectionFactory;
use Emartech\Emarsys\Model\ResourceModel\Event\Collection;

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
     * @param EventRepository $eventRepository
     * @param CollectionFactory $eventCollectionFactory
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
     * @param string $sinceId
     * @param int $pageSize
     *
     * @return EventsApiResponseInterface
     * @throws \Magento\Framework\Webapi\Exception
     */
    public function get($sinceId, $pageSize)
    {
        $this->validateSinceId($sinceId);

        $this
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
    private function handleEvents()
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
    private function initCollection()
    {
        $this->eventCollection = $this->eventCollectionFactory->create();

        return $this;
    }

    /**
     * @param int $sinceId
     *
     * @return $this
     */
    private function getEvents($sinceId)
    {
        $this->eventCollection
            ->addFieldToFilter('event_id', ['gt' => $sinceId]);

        return $this;
    }

    /**
     * @return $this
     */
    private function setOrder()
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
    private function setPageSize($pageSize)
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
    private function removeOldEvents($beforeId)
    {
        $this->eventRepository->deleteUntilSinceId($beforeId);

        return $this;
    }

    /**
     * @param $sinceId
     * @throws \Magento\Framework\Webapi\Exception
     */
    private function validateSinceId($sinceId)
    {
        if ($this->eventRepository->isSinceIdIsHigherThanAutoIncrement($sinceId)) {
            throw new \Magento\Framework\Webapi\Exception(
                __('sinceId is higher than auto-increment'),
                \Magento\Framework\Webapi\Exception::HTTP_NOT_ACCEPTABLE,
                \Magento\Framework\Webapi\Exception::HTTP_NOT_ACCEPTABLE
            );
        }
    }
}

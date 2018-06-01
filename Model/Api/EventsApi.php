<?php


namespace Emartech\Emarsys\Model\Api;


use Emartech\Emarsys\Api\EventsApiInterface;
use Emartech\Emarsys\Model\ResourceModel\Event\CollectionFactory;
use Emartech\Emarsys\Model\ResourceModel\Event\Collection;

class EventsApi implements EventsApiInterface
{
  /**
   * @var EventFactory
   */
  private $eventCollectionFactory;

  public function __construct(CollectionFactory $eventCollectionFactory)
  {
    $this->eventCollectionFactory = $eventCollectionFactory;
  }

  /**
   * @param int $since_id
   * @param int $page_size
   * @return mixed
   */
  public function get($since_id, $page_size)
  {
    $this->removeOldEvents($since_id);

    /** @var Collection $eventCollection */
    $eventCollection = $this->eventCollectionFactory->create()
      ->addFieldToFilter('event_id', ['gt' => $since_id])
      ->setPageSize($page_size);

    $sinceEvents = [];
    foreach ($eventCollection as $event) {
      $sinceEvents[] = [
        'event_id' => $event->getEventId(),
        'event_type' => $event->getEventType(),
        'event_data' => json_decode($event->getEventData()),
        'created_at' => $event->getCreatedAt()
      ];
    }

    $responseData = [[
      'events' => $sinceEvents,
      'current_page' => $eventCollection->getCurPage(),
      'last_page' => $eventCollection->getLastPageNumber(),
      'page_size' => $page_size
    ]];

    return $responseData;
  }

  private function removeOldEvents($before_id)
  {
    /** @var Collection $oldEvents */
    $oldEvents = $this->eventCollectionFactory->create()
      ->addFieldToFilter('event_id', ['lteq' => $before_id]);

    $oldEvents->walk('delete');

    $oldEvents->clear();
  }
}
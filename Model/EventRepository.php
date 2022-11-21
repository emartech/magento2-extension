<?php


namespace Emartech\Emarsys\Model;

use Emartech\Emarsys\Api\Data\EventInterface;
use Emartech\Emarsys\Api\EventRepositoryInterface;
use Emartech\Emarsys\Model\EventFactory as EventFactory;
use Emartech\Emarsys\Model\ResourceModel\Event as EventResourceModel;
use Emartech\Emarsys\Model\ResourceModel\Event\CollectionFactory as EventCollectionFactory;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;

class EventRepository implements EventRepositoryInterface
{

    /**
     * @var EventFactory
     */
    private $eventFactory;

    /**
     * @var EventResourceModel
     */
    private $eventResourceModel;

    /**
     * @var EventCollectionFactory
     */
    private $eventCollectionFactory;

    /**
     * EventRepository constructor.
     *
     * @param EventFactory           $eventFactory
     * @param EventResourceModel     $eventResourceModel
     * @param EventCollectionFactory $eventCollectionFactory
     */
    public function __construct(
        EventFactory $eventFactory,
        EventResourceModel $eventResourceModel,
        EventCollectionFactory $eventCollectionFactory
    ) {
        $this->eventFactory = $eventFactory;
        $this->eventResourceModel = $eventResourceModel;
        $this->eventCollectionFactory = $eventCollectionFactory;
    }

    /**
     * Get
     *
     * @param int $id
     *
     * @return EventInterface
     * @throws NoSuchEntityException
     */
    public function get(int $id): EventInterface
    {
        $event = $this->eventFactory->create()->load($id);
        if (!$event->getId()) {
            throw new NoSuchEntityException(__('Requested Event doesn\'t exist'));
        }

        return $event;
    }

    /**
     * Save
     *
     * @param EventInterface $event
     *
     * @return EventInterface
     * @throws AlreadyExistsException
     */
    public function save(EventInterface $event): EventInterface
    {
        $this->eventResourceModel->save($event);

        return $event;
    }

    /**
     * IsSinceIdIsHigherThanAutoIncrement
     *
     * @param int $sinceId
     *
     * @return bool
     */
    public function isSinceIdIsHigherThanAutoIncrement(int $sinceId): bool
    {
        $eventsTableName = $this->eventResourceModel->getTable('emarsys_events_data');
        $query = sprintf(
            "SELECT (SELECT COALESCE(MAX(event_id), CAST(? AS UNSIGNED)) FROM %s) < CAST(? AS UNSIGNED);",
            $eventsTableName
        );

        return (bool) $this->eventResourceModel->getConnection()->fetchOne(
            $query,
            [$sinceId, $sinceId]
        );
    }

    /**
     * DeleteUntilSinceId
     *
     * @param int $sinceId
     *
     * @return void
     */
    public function deleteUntilSinceId(int $sinceId): void
    {
        $eventsTableName = $this->eventResourceModel->getTable('emarsys_events_data');
        $query = sprintf(
            "DELETE FROM %s WHERE event_id <= ?;",
            $eventsTableName
        );
        $this->eventResourceModel->getConnection()->query(
            $query,
            [$sinceId]
        );
    }
}

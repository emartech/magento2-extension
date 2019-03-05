<?php


namespace Emartech\Emarsys\Model;

use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;

use Emartech\Emarsys\Api\Data\EventInterface;
use Emartech\Emarsys\Model\EventFactory as EventFactory;
use Emartech\Emarsys\Model\ResourceModel\Event as EventResourceModel;
use Emartech\Emarsys\Model\ResourceModel\Event\CollectionFactory as EventCollectionFactory;
use Emartech\Emarsys\Api\EventRepositoryInterface;

/**
 * Class EventRepository
 * @package Emartech\Emarsys\Model
 */
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
     * @param EventFactory                  $eventFactory
     * @param EventResourceModel            $eventResourceModel
     * @param EventCollectionFactory        $eventCollectionFactory
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
     * @param $id
     *
     * @return EventInterface
     * @throws NoSuchEntityException
     */
    public function get($id)
    {
        /** @var \Emartech\Emarsys\Model\Event $event */
        $event = $this->eventFactory->create()->load($id);
        if (!$event->getId()) {
            throw new NoSuchEntityException(__('Requested Event doesn\'t exist'));
        }
        return $event;
    }

    /**
     * @param EventInterface $event
     *
     * @return EventInterface
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save(EventInterface $event)
    {
        /** @var \Emartech\Emarsys\Model\Event $event */
        $this->eventResourceModel->save($event);

        return $event;
    }

    /**
     * @param string sinceId
     * @return bool
     */
    public function isSinceIdIsHigherThanAutoIncrement($sinceId)
    {
        return (bool) $this->eventResourceModel->getConnection()->fetchOne("
            SELECT
                (
                    SELECT
                        `AUTO_INCREMENT`
                    FROM
                        INFORMATION_SCHEMA.TABLES
                    WHERE
                        TABLE_SCHEMA = (
                            SELECT
                                database()
                        )
                        AND TABLE_NAME = 'emartech_events_data'
                ) <= (
                    SELECT
                        CAST(? AS UNSIGNED)
                );
        ", [$sinceId]);
    }
}

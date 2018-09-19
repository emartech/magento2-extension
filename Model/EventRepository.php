<?php


namespace Emartech\Emarsys\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\InputException;

use Emartech\Emarsys\Api\Data\EventInterface;
use Emartech\Emarsys\Model\EventFactory as EventFactory;
use Emartech\Emarsys\Model\ResourceModel\Event as EventResourceModel;
use Emartech\Emarsys\Model\ResourceModel\Event\CollectionFactory as EventCollectionFactory;
use Emartech\Emarsys\Model\ResourceModel\Event\Collection as EventCollection;
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
     * @var SearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var EventCollectionFactory
     */
    private $eventCollectionFactory;

    private $collectionProcessor;

    /**
     * EventRepository constructor.
     *
     * @param EventFactory                  $eventFactory
     * @param EventResourceModel            $eventResourceModel
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     * @param EventCollectionFactory        $eventCollectionFactory
     * @param CollectionProcessorInterface  $collectionProcessor
     */
    public function __construct(
        EventFactory $eventFactory,
        EventResourceModel $eventResourceModel,
        SearchResultsInterfaceFactory $searchResultsFactory,
        EventCollectionFactory $eventCollectionFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->eventFactory = $eventFactory;
        $this->eventResourceModel = $eventResourceModel;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->eventCollectionFactory = $eventCollectionFactory;
        $this->collectionProcessor = $collectionProcessor;
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
     * @param string                  $eventType
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return SearchResultsInterface
     * @throws InputException
     */
    public function getList($eventType, SearchCriteriaInterface $searchCriteria)
    {
        if (!$eventType) {
            throw InputException::requiredField('entity_type');
        }

        /** @var EventCollection $eventCollection */
        $eventCollection = $this->eventCollectionFactory->create();
        $eventCollection->addFieldToFilter('entity_type_code', ['eq' => $eventType]);

        $this->collectionProcessor->process($searchCriteria, $eventCollection);

        /** @var SearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($eventCollection->getItems());
        $searchResults->setTotalCount($eventCollection->getSize());
        return $searchResults;
    }
}
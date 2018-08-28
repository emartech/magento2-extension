<?php


namespace Emartech\Emarsys\Model;


use Emartech\Emarsys\Api\Data\EventInterface;
use Emartech\Emarsys\Api\EventRepositoryInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;

class EventRepository implements EventRepositoryInterface
{
    /**
     * @var ResourceModel\Event
     */
    public $eventResource;

    /**
     * @var SearchResultsInterfaceFactory
     */
    public $searchResultsFactory;

    /**
     * @var ResourceModel\Event\CollectionFactory
     */
    public $collectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    public $collectionProcessor;

    /**
     * EventRepository constructor.
     * @param ResourceModel\Event $eventResource
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     * @param ResourceModel\Event\CollectionFactory $collectionFactory
     * @param CollectionProcessorInterface|null $collectionProcessor
     */
    public function __construct(
        ResourceModel\Event $eventResource,
        SearchResultsInterfaceFactory $searchResultsFactory,
        ResourceModel\Event\CollectionFactory $collectionFactory,
        CollectionProcessorInterface $collectionProcessor = null
    )
    {
        $this->eventResource = $eventResource;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
    }


    /**
     * @param $id
     * @return ResourceModel\Event
     * @throws NoSuchEntityException
     */
    public function get($id)
    {
        $event = $this->eventResource->load($id);
        if (!$event->getId()) {
            throw new NoSuchEntityException(__('Requested Event doesn\'t exist'));
        }
        return $event;
    }

  /**
   * @param EventInterface $event
   * @return EventInterface
   * @throws \Exception
   * @throws \Magento\Framework\Exception\AlreadyExistsException
   */
    public function save(EventInterface $event)
    {
        $this->eventResource->save($event);
        return $event;
    }

    /**
     * Retrieve all Events for entity type
     *
     * @param string $eventType
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterfaceFactory
     */
    public function getList($eventType, SearchCriteriaInterface $searchCriteria)
    {
        if (!$eventType) {
            throw InputException::requiredField('entity_type');
        }

        /** @var \Emartech\Emarsys\Model\ResourceModel\Event\CollectionFactory $eventCollection */
        $eventCollection = $this->collectionFactory->create();
        $eventCollection->addFieldToFilter('entity_type_code', ['eq' => $eventType]);

        $this->collectionProcessor->process($searchCriteria, $eventCollection);

        /** @var SearchResultsInterfaceFactory $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($eventCollection->getItems());
        $searchResults->setTotalCount($eventCollection->getSize());
        return $searchResults;
    }
}
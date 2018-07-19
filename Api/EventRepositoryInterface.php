<?php


namespace Emartech\Emarsys\Api;


use Emartech\Emarsys\Api\Data\EventInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface EventRepositoryInterface
{
    /**
     * @param $id
     * @return EventInterface
     */
    public function get($id);

    /**
     * @param EventInterface $event
     * @return EventInterface
     */
    public function save(EventInterface $event);

    /**
     * Retrieve all Events for entity type
     *
     * @param string $eventType
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getList($eventType, SearchCriteriaInterface $searchCriteria);
}
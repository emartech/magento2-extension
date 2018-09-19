<?php

namespace Emartech\Emarsys\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

use Emartech\Emarsys\Api\Data\EventInterface;

/**
 * Interface EventRepositoryInterface
 * @package Emartech\Emarsys\Api
 */
interface EventRepositoryInterface
{
    /**
     * @param $id
     *
     * @return \Emartech\Emarsys\Api\Data\EventInterface
     */
    public function get($id);

    /**
     * @param \Emartech\Emarsys\Api\Data\EventInterface $event
     *
     * @return \Emartech\Emarsys\Api\Data\EventInterface
     */
    public function save(EventInterface $event);

    /**
     * Retrieve all Events for entity type
     *
     * @param string                                         $eventType
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getList($eventType, SearchCriteriaInterface $searchCriteria);
}

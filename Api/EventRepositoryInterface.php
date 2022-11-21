<?php

namespace Emartech\Emarsys\Api;

use Emartech\Emarsys\Api\Data\EventInterface;

interface EventRepositoryInterface
{
    /**
     * Get
     *
     * @param int $id
     *
     * @return \Emartech\Emarsys\Api\Data\EventInterface
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(int $id): EventInterface;

    /**
     * Save
     *
     * @param \Emartech\Emarsys\Api\Data\EventInterface $event
     *
     * @return \Emartech\Emarsys\Api\Data\EventInterface
     *
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save(EventInterface $event): EventInterface;

    /**
     * IsSinceIdIsHigherThanAutoIncrement
     *
     * @param int $sinceId
     *
     * @return bool
     */
    public function isSinceIdIsHigherThanAutoIncrement(int $sinceId): bool;

    /**
     * DeleteUntilSinceId
     *
     * @param int $sinceId
     *
     * @return void
     */
    public function deleteUntilSinceId(int $sinceId): void;
}

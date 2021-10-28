<?php

namespace Emartech\Emarsys\Api;

use Emartech\Emarsys\Api\Data\EventInterface;

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
     * @param string sinceId
     * @return bool
     */
    public function isSinceIdIsHigherThanAutoIncrement($sinceId);

    /**
     * @param string sinceId
     * @return void
     */
    public function deleteUntilSinceId($sinceId);
}

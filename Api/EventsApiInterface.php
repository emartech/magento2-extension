<?php

namespace Emartech\Emarsys\Api;

use Emartech\Emarsys\Api\Data\EventsApiResponseInterface;

interface EventsApiInterface
{
    /**
     * Get
     *
     * @param int $sinceId
     * @param int $pageSize
     *
     * @return \Emartech\Emarsys\Api\Data\EventsApiResponseInterface
     */
    public function get(int $sinceId, int $pageSize): EventsApiResponseInterface;
}

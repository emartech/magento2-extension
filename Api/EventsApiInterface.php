<?php

namespace Emartech\Emarsys\Api;

use Emartech\Emarsys\Api\Data\EventsApiResponseInterface;

interface EventsApiInterface
{
    /**
     * @param string $sinceId
     * @param int $pageSize
     *
     * @return \Emartech\Emarsys\Api\Data\EventsApiResponseInterface
     */
    public function get($sinceId, $pageSize);
}

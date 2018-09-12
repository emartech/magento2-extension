<?php

namespace Emartech\Emarsys\Api;

use Emartech\Emarsys\Api\Data\EventsApiResponseInterface;

/**
 * Interface EventsApiInterface
 * @package Emartech\Emarsys\Api
 */
interface EventsApiInterface
{
    /**
     * @param int $sinceId
     * @param int $pageSize
     *
     * @return \Emartech\Emarsys\Api\Data\EventsApiResponseInterface
     */
    public function get($sinceId, $pageSize): EventsApiResponseInterface;
}

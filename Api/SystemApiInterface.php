<?php

namespace Emartech\Emarsys\Api;

/**
 * Interface SystemApiInterface
 * @package Emartech\Emarsys\Api
 */
interface SystemApiInterface
{
    /**
     * @return \Emartech\Emarsys\Api\Data\SystemApiResponseInterface
     */
    public function get();
}

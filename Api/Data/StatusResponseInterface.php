<?php

namespace Emartech\Emarsys\Api\Data;

/**
 * Interface StatusResponseInterface
 * @package Emartech\Emarsys\Api\Data
 */
interface StatusResponseInterface
{
    const STATUS_KEY   = 'status';

    /**
     * @return string
     */
    public function getStatus(): string;

    /**
     * @param string $status
     *
     * @return $this
     */
    public function setStatus($status): StatusResponseInterface;
}

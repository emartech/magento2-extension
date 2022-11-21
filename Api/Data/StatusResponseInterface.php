<?php

namespace Emartech\Emarsys\Api\Data;

interface StatusResponseInterface extends ErrorResponseInterface
{
    public const STATUS_KEY = 'status';

    /**
     * GetStatus
     *
     * @return string
     */
    public function getStatus(): string;

    /**
     * SetStatus
     *
     * @param string $status
     *
     * @return \Emartech\Emarsys\Api\Data\StatusResponseInterface
     */
    public function setStatus(string $status): StatusResponseInterface;
}

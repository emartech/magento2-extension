<?php

namespace Emartech\Emarsys\Api\Data;

interface StatusResponseInterface extends ErrorResponseInterface
{
    const STATUS_KEY = 'status';

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @param string $status
     *
     * @return $this
     */
    public function setStatus($status);
}

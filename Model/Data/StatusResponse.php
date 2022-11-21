<?php

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\StatusResponseInterface;

class StatusResponse extends ErrorResponse implements StatusResponseInterface
{
    /**
     * GetStatus
     *
     * @return string
     */
    public function getStatus(): string
    {
        return (string) $this->getData(self::STATUS_KEY);
    }

    /**
     * SetStatus
     *
     * @param string $status
     *
     * @return $this
     */
    public function setStatus(string $status): StatusResponseInterface
    {
        $this->setData(self::STATUS_KEY, $status);

        return $this;
    }
}

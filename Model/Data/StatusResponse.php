<?php

namespace Emartech\Emarsys\Model\Data;

/**
 * Class StatusResponse
 * @package Emartech\Emarsys\Model\Data
 */
class StatusResponse extends \Magento\Framework\DataObject implements \Emartech\Emarsys\Api\Data\StatusResponseInterface
{
    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS_KEY);
    }

    /**
     * @param string $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->setData(self::STATUS_KEY, $status);
        return $this;
    }
}

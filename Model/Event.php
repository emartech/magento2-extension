<?php

namespace Emartech\Emarsys\Model;

use Magento\Framework\Model\AbstractModel;

use Emartech\Emarsys\Api\Data\EventInterface;

/**
 * Class Event
 * @package Emartech\Emarsys\Model
 */
class Event extends AbstractModel implements EventInterface
{
    /**
     * @return int
     */
    public function getEventId()
    {
        return $this->_getData(self::EVENT_ID_KEY);
    }

    /**
     * @return string
     */
    public function getEventType()
    {
        return $this->_getData(self::EVENT_TYPE_KEY);
    }

    /**
     * @return string
     */
    public function getEventData()
    {
        return $this->_getData(self::EVENT_DATA_KEY);
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->_getData(self::CREATED_AT_KEY);
    }

    /**
     * @param int $eventId
     *
     * @return $this
     */
    public function setEventId($eventId)
    {
        $this->setData(self::EVENT_ID_KEY, $eventId);

        return $this;
    }

    /**
     * @param string $eventType
     *
     * @return $this
     */
    public function setEventType($eventType)
    {
        $this->setData(self::EVENT_TYPE_KEY, $eventType);

        return $this;
    }

    /**
     * @param string $eventData
     *
     * @return $this
     */
    public function setEventData($eventData)
    {
        $this->setData(self::EVENT_DATA_KEY, $eventData);

        return $this;
    }

    /**
     * @param string $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->setData(self::CREATED_AT_KEY, $createdAt);

        return $this;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Emartech\Emarsys\Model\ResourceModel\Event');
    }
}
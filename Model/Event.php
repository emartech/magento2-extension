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
    public function getEventId(): int
    {
        return $this->_getData(self::EVENT_ID_KEY);
    }

    /**
     * @return string
     */
    public function getEventType(): string
    {
        return $this->_getData(self::EVENT_TYPE_KEY);
    }

    /**
     * @return string
     */
    public function getEventData(): string
    {
        return $this->_getData(self::EVENT_DATA_KEY);
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->_getData(self::CREATED_AT_KEY);
    }

    /**
     * @return int
     */
    public function getWebsiteId(): int
    {
        return $this->_getData(self::WEBSITE_ID_KEY);
    }

    /**
     * @return int
     */
    public function getStoreId(): int
    {
        return $this->_getData(self::STORE_ID_KEY);
    }

    /**
     * @param int $eventId
     *
     * @return $this
     */
    public function setEventId($eventId): EventInterface
    {
        $this->setData(self::EVENT_ID_KEY, $eventId);

        return $this;
    }

    /**
     * @param string $eventType
     *
     * @return $this
     */
    public function setEventType($eventType): EventInterface
    {
        $this->setData(self::EVENT_TYPE_KEY, $eventType);

        return $this;
    }

    /**
     * @param string $eventData
     *
     * @return $this
     */
    public function setEventData($eventData): EventInterface
    {
        $this->setData(self::EVENT_DATA_KEY, $eventData);

        return $this;
    }

    /**
     * @param string $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt): EventInterface
    {
        $this->setData(self::CREATED_AT_KEY, $createdAt);

        return $this;
    }

    /**
     * @param int $websiteId
     *
     * @return $this
     */
    public function setWebsiteId($websiteId): EventInterface
    {
        $this->setData(self::WEBSITE_ID_KEY, $websiteId);

        return $this;
    }

    /**
     * @param int $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId): EventInterface
    {
        $this->setData(self::STORE_ID_KEY, $storeId);

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
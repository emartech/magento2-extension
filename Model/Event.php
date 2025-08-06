<?php

namespace Emartech\Emarsys\Model;

use Emartech\Emarsys\Api\Data\EventInterface;
use Magento\Framework\Model\AbstractModel;

class Event extends AbstractModel implements EventInterface
{
    /**
     * GetEventId
     *
     * @return int
     */
    public function getEventId(): int
    {
        return (int) $this->_getData(self::EVENT_ID_KEY);
    }

    /**
     * GetEventType
     *
     * @return string|null
     */
    public function getEventType(): ?string
    {
        return $this->_getData(self::EVENT_TYPE_KEY);
    }

    /**
     * GetEventData
     *
     * @return string|null
     */
    public function getEventData(): ?string
    {
        return $this->_getData(self::EVENT_DATA_KEY);
    }

    /**
     * GetCreatedAt
     *
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->_getData(self::CREATED_AT_KEY);
    }

    /**
     * GetWebsiteId
     *
     * @return int|null
     */
    public function getWebsiteId(): ?int
    {
        return $this->_getData(self::WEBSITE_ID_KEY);
    }

    /**
     * GetStoreId
     *
     * @return int|null
     */
    public function getStoreId(): ?int
    {
        return $this->_getData(self::STORE_ID_KEY);
    }

    /**
     * GetEntityId
     *
     * @return int
     */
    public function getEntityId(): int
    {
        return (int)$this->_getData(self::ENTITY_ID_KEY);
    }

    /**
     * SetEventId
     *
     * @param int $eventId
     *
     * @return EventInterface
     */
    public function setEventId(int $eventId): EventInterface
    {
        $this->setData(self::EVENT_ID_KEY, $eventId);

        return $this;
    }

    /**
     * SetEventType
     *
     * @param string|null $eventType
     *
     * @return EventInterface
     */
    public function setEventType(?string $eventType = null): EventInterface
    {
        $this->setData(self::EVENT_TYPE_KEY, $eventType);

        return $this;
    }

    /**
     * SetEventData
     *
     * @param string|null $eventData
     *
     * @return EventInterface
     */
    public function setEventData(?string $eventData = null): EventInterface
    {
        $this->setData(self::EVENT_DATA_KEY, $eventData);

        return $this;
    }

    /**
     * SetCreatedAt
     *
     * @param string|null $createdAt
     *
     * @return EventInterface
     */
    public function setCreatedAt(?string $createdAt = null): EventInterface
    {
        $this->setData(self::CREATED_AT_KEY, $createdAt);

        return $this;
    }

    /**
     * SetWebsiteId
     *
     * @param int|null $websiteId
     *
     * @return EventInterface
     */
    public function setWebsiteId(?int $websiteId = null): EventInterface
    {
        $this->setData(self::WEBSITE_ID_KEY, $websiteId);

        return $this;
    }

    /**
     * SetStoreId
     *
     * @param int|null $storeId
     *
     * @return EventInterface
     */
    public function setStoreId(?int $storeId = null): EventInterface
    {
        $this->setData(self::STORE_ID_KEY, $storeId);

        return $this;
    }

    /**
     * SetEntityId
     *
     * @param int $entityId
     *
     * @return EventInterface
     */
    public function setEntityId($entityId): EventInterface
    {
        $this->setData(self::ENTITY_ID_KEY, $entityId);

        return $this;
    }

    /**
     * _construct
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Event::class);
    }
}

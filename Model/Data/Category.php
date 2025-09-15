<?php

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\CategoryInterface;
use Emartech\Emarsys\Api\Data\CategoryStoreDataInterface;
use Magento\Framework\DataObject;

class Category extends DataObject implements CategoryInterface
{

    /**
     * GetStoreData
     *
     * @return CategoryStoreDataInterface[]|null
     */
    public function getStoreData(): ?array
    {
        return $this->getData(self::STORE_DATA_KEY);
    }

    /**
     * GetChildrenCount
     *
     * @return int|null
     */
    public function getChildrenCount(): ?int
    {
        return $this->getData(self::CHILDREN_COUNT_KEY);
    }

    /**
     * GetEntityId
     *
     * @return int
     */
    public function getEntityId(): int
    {
        return (int) $this->getData(self::ENTITY_ID_KEY);
    }

    /**
     * GetPath
     *
     * @retrn string|null
     */
    public function getPath(): ?string
    {
        return $this->getData(self::PATH_KEY);
    }

    /**
     * SetChildrenCount
     *
     * @param int|null $childrenCount
     *
     * @return CategoryInterface
     */
    public function setChildrenCount(?int $childrenCount = null): CategoryInterface
    {
        $this->setData(self::CHILDREN_COUNT_KEY, $childrenCount);

        return $this;
    }

    /**
     * SetEntityId
     *
     * @param int $entityId
     *
     * @return CategoryInterface
     */
    public function setEntityId(int $entityId): CategoryInterface
    {
        $this->setData(self::ENTITY_ID_KEY, $entityId);

        return $this;
    }

    /**
     * SetPath
     *
     * @param string|null $path
     *
     * @return CategoryInterface
     */
    public function setPath(?string $path = null): CategoryInterface
    {
        $this->setData(self::PATH_KEY, $path);

        return $this;
    }

    /**
     * SetStoreData
     *
     * @param CategoryStoreDataInterface[]|null $storeData
     *
     * @return CategoryInterface
     */
    public function setStoreData(?array $storeData = null): CategoryInterface
    {
        $this->setData(self::STORE_DATA_KEY, $storeData);

        return $this;
    }
}

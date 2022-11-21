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
     * @return CategoryStoreDataInterface[]
     */
    public function getStoreData(): array
    {
        return $this->getData(self::STORE_DATA_KEY);
    }

    /**
     * GetChildrenCount
     *
     * @return int
     */
    public function getChildrenCount(): int
    {
        return (int) $this->getData(self::CHILDREN_COUNT_KEY);
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
     * @retrn string
     */
    public function getPath(): string
    {
        return (string) $this->getData(self::PATH_KEY);
    }

    /**
     * SetChildrenCount
     *
     * @param int $childrenCount
     *
     * @return CategoryInterface
     */
    public function setChildrenCount(int $childrenCount): CategoryInterface
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
     * @param string $path
     *
     * @return CategoryInterface
     */
    public function setPath(string $path): CategoryInterface
    {
        $this->setData(self::PATH_KEY, $path);

        return $this;
    }

    /**
     * SetStoreData
     *
     * @param CategoryStoreDataInterface[] $storeData
     *
     * @return CategoryInterface
     */
    public function setStoreData(array $storeData): CategoryInterface
    {
        $this->setData(self::STORE_DATA_KEY, $storeData);

        return $this;
    }
}

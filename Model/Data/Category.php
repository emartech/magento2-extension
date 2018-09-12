<?php

namespace Emartech\Emarsys\Model\Data;

use Magento\Framework\DataObject;

use Emartech\Emarsys\Api\Data\CategoryInterface;
use Emartech\Emarsys\Api\Data\CategoryStoreDataInterface;

/**
 * Class Product
 * @package Emartech\Emarsys\Model\Data
 */
class Category extends DataObject implements CategoryInterface
{

    /**
     * @return CategoryStoreDataInterface[]
     */
    public function getStoreData(): array
    {
        return $this->getData(self::STORE_DATA_KEY);
    }

    /**
     * @return int
     */
    public function getChildrenCount(): int
    {
        return $this->getData(self::CHILDREN_COUNT_KEY);
    }

    /**
     * @return int
     */
    public function getEntityId(): int
    {
        return $this->getData(self::ENTITY_ID_KEY);
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->getData(self::PATH_KEY);
    }

    /**
     * @param int $childrenCount
     *
     * @return $this
     */
    public function setChildrenCount($childrenCount): CategoryInterface
    {
        $this->setData(self::CHILDREN_COUNT_KEY, $childrenCount);

        return $this;
    }

    /**
     * @param int $entityId
     *
     * @return $this
     */
    public function setEntityId($entityId): CategoryInterface
    {
        $this->setData(self::ENTITY_ID_KEY, $entityId);

        return $this;
    }

    /**
     * @param string $path
     *
     * @return $this
     */
    public function setPath($path): CategoryInterface
    {
        $this->setData(self::PATH_KEY, $path);

        return $this;
    }

    /**
     * @param CategoryStoreDataInterface[] $storeData
     *
     * @return $this
     */
    public function setStoreData($storeData): CategoryInterface
    {
        $this->setData(self::STORE_DATA_KEY, $storeData);

        return $this;
    }
}

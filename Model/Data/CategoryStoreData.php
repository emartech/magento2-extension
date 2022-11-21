<?php

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\CategoryStoreDataInterface;
use Magento\Framework\DataObject;

class CategoryStoreData extends DataObject implements CategoryStoreDataInterface
{
    /**
     * GetDescription
     *
     * @return string
     */
    public function getDescription(): string
    {
        return (string) $this->getData(self::DESCRIPTION_KEY);
    }

    /**
     * GetName
     *
     * @return string
     */
    public function getName(): string
    {
        return (string) $this->getData(self::NAME_KEY);
    }

    /**
     * GetImage
     *
     * @return string
     */
    public function getImage(): string
    {
        return (string) $this->getData(self::IMAGE_KEY);
    }

    /**
     * GetIsActive
     *
     * @return int
     */
    public function getIsActive(): int
    {
        return (int) $this->getData(self::IS_ACTIVE_KEY);
    }

    /**
     * GetStoreId
     *
     * @return int
     */
    public function getStoreId(): int
    {
        return (int) $this->getData(self::STORE_ID_KEY);
    }

    /**
     * SetDescription
     *
     * @param string $description
     *
     * @return CategoryStoreDataInterface
     */
    public function setDescription(string $description): CategoryStoreDataInterface
    {
        $this->setData(self::DESCRIPTION_KEY, $description);

        return $this;
    }

    /**
     * SetName
     *
     * @param string $name
     *
     * @return CategoryStoreDataInterface
     */
    public function setName(string $name): CategoryStoreDataInterface
    {
        $this->setData(self::NAME_KEY, $name);

        return $this;
    }

    /**
     * SetImage
     *
     * @param string $image
     *
     * @return CategoryStoreDataInterface
     */
    public function setImage(string $image): CategoryStoreDataInterface
    {
        $this->setData(self::IMAGE_KEY, $image);

        return $this;
    }

    /**
     * SetIsActive
     *
     * @param int $isActive
     *
     * @return CategoryStoreDataInterface
     */
    public function setIsActive(int $isActive): CategoryStoreDataInterface
    {
        $this->setData(self::IS_ACTIVE_KEY, $isActive);

        return $this;
    }

    /**
     * SetStoreId
     *
     * @param int $storeId
     *
     * @return CategoryStoreDataInterface
     */
    public function setStoreId(int $storeId): CategoryStoreDataInterface
    {
        $this->setData(self::STORE_ID_KEY, $storeId);

        return $this;
    }
}

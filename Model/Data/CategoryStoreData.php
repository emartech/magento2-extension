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
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->getData(self::NAME_KEY);
    }

    /**
     * GetImage
     *
     * @return string|null
     */
    public function getImage(): ?string
    {
        return $this->getData(self::IMAGE_KEY);
    }

    /**
     * GetIsActive
     *
     * @return int|null
     */
    public function getIsActive(): ?int
    {
        return $this->getData(self::IS_ACTIVE_KEY);
    }

    /**
     * GetStoreId
     *
     * @return int|null
     */
    public function getStoreId(): ?int
    {
        return $this->getData(self::STORE_ID_KEY);
    }

    /**
     * SetDescription
     *
     * @param string|null $description
     *
     * @return CategoryStoreDataInterface
     */
    public function setDescription(?string $description = null): CategoryStoreDataInterface
    {
        $this->setData(self::DESCRIPTION_KEY, $description);

        return $this;
    }

    /**
     * SetName
     *
     * @param string|null $name
     *
     * @return CategoryStoreDataInterface
     */
    public function setName(?string $name = null): CategoryStoreDataInterface
    {
        $this->setData(self::NAME_KEY, $name);

        return $this;
    }

    /**
     * SetImage
     *
     * @param string|null $image
     *
     * @return CategoryStoreDataInterface
     */
    public function setImage(?string $image = null): CategoryStoreDataInterface
    {
        $this->setData(self::IMAGE_KEY, $image);

        return $this;
    }

    /**
     * SetIsActive
     *
     * @param int|null $isActive
     *
     * @return CategoryStoreDataInterface
     */
    public function setIsActive(?int $isActive = null): CategoryStoreDataInterface
    {
        $this->setData(self::IS_ACTIVE_KEY, $isActive);

        return $this;
    }

    /**
     * SetStoreId
     *
     * @param int|null $storeId
     *
     * @return CategoryStoreDataInterface
     */
    public function setStoreId(?int $storeId = null): CategoryStoreDataInterface
    {
        $this->setData(self::STORE_ID_KEY, $storeId);

        return $this;
    }
}

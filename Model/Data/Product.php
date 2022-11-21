<?php

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\ImagesInterface;
use Emartech\Emarsys\Api\Data\ProductInterface;
use Emartech\Emarsys\Api\Data\ProductStoreDataInterface;
use Magento\Framework\DataObject;

class Product extends DataObject implements ProductInterface
{
    /**
     * GetStoreData
     *
     * @return ProductStoreDataInterface[]
     */
    public function getStoreData(): array
    {
        return $this->getData(self::STORE_DATA_KEY);
    }

    /**
     * GetCategories
     *
     * @return string[]
     */
    public function getCategories(): array
    {
        return $this->getData(self::CATEGORIES_KEY);
    }

    /**
     * GetChildrenEntityIds
     *
     * @return int[]
     */
    public function getChildrenEntityIds(): array
    {
        return $this->getData(self::CHILDREN_ENTITY_IDS_KEY);
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
     * GetImages
     *
     * @return ImagesInterface
     */
    public function getImages(): ImagesInterface
    {
        return $this->getData(self::IMAGES_KEY);
    }

    /**
     * GetIsInStock
     *
     * @return int
     */
    public function getIsInStock(): int
    {
        return (int) $this->getData(self::IS_IN_STOCK_KEY);
    }

    /**
     * GetQty
     *
     * @return float
     */
    public function getQty(): float
    {
        return (float) $this->getData(self::QTY_KEY);
    }

    /**
     * GetSku
     *
     * @return string
     */
    public function getSku(): string
    {
        return (string) $this->getData(self::SKU_KEY);
    }

    /**
     * GetType
     *
     * @return string
     */
    public function getType(): string
    {
        return (string) $this->getData(self::TYPE_KEY);
    }

    /**
     * SetStoreData
     *
     * @param ProductStoreDataInterface[] $storeData
     *
     * @return ProductInterface
     */
    public function setStoreData(array $storeData): ProductInterface
    {
        $this->setData(self::STORE_DATA_KEY, $storeData);

        return $this;
    }

    /**
     * SetCategories
     *
     * @param string[] $categories
     *
     * @return ProductInterface
     */
    public function setCategories(array $categories): ProductInterface
    {
        $this->setData(self::CATEGORIES_KEY, $categories);

        return $this;
    }

    /**
     * SetChildrenEntityIds
     *
     * @param int[] $childrenEntityIds
     *
     * @return ProductInterface
     */
    public function setChildrenEntityIds(array $childrenEntityIds): ProductInterface
    {
        $this->setData(self::CHILDREN_ENTITY_IDS_KEY, $childrenEntityIds);

        return $this;
    }

    /**
     * SetEntityId
     *
     * @param int $entityId
     *
     * @return ProductInterface
     */
    public function setEntityId(int $entityId): ProductInterface
    {
        $this->setData(self::ENTITY_ID_KEY, $entityId);

        return $this;
    }

    /**
     * SetImages
     *
     * @param ImagesInterface $images
     *
     * @return ProductInterface
     */
    public function setImages(ImagesInterface $images): ProductInterface
    {
        $this->setData(self::IMAGES_KEY, $images);

        return $this;
    }

    /**
     * SetIsInStock
     *
     * @param int $isInStock
     *
     * @return ProductInterface
     */
    public function setIsInStock(int $isInStock): ProductInterface
    {
        $this->setData(self::IS_IN_STOCK_KEY, $isInStock);

        return $this;
    }

    /**
     * SetQty
     *
     * @param float $qty
     *
     * @return ProductInterface
     */
    public function setQty(float $qty): ProductInterface
    {
        $this->setData(self::QTY_KEY, $qty);

        return $this;
    }

    /**
     * SetSku
     *
     * @param string $sku
     *
     * @return ProductInterface
     */
    public function setSku(string $sku): ProductInterface
    {
        $this->setData(self::SKU_KEY, $sku);

        return $this;
    }

    /**
     * SetType
     *
     * @param string $type
     *
     * @return ProductInterface
     */
    public function setType(string $type): ProductInterface
    {
        $this->setData(self::TYPE_KEY, $type);

        return $this;
    }
}

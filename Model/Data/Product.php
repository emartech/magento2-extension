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
     * @return ProductStoreDataInterface[]|null
     */
    public function getStoreData(): ?array
    {
        return $this->getData(self::STORE_DATA_KEY);
    }

    /**
     * GetCategories
     *
     * @return string[]|null
     */
    public function getCategories(): ?array
    {
        return $this->getData(self::CATEGORIES_KEY);
    }

    /**
     * GetChildrenEntityIds
     *
     * @return int[]|null
     */
    public function getChildrenEntityIds(): ?array
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
     * @return ImagesInterface|null
     */
    public function getImages(): ?ImagesInterface
    {
        return $this->getData(self::IMAGES_KEY);
    }

    /**
     * GetIsInStock
     *
     * @return int|null
     */
    public function getIsInStock(): ?int
    {
        return $this->getData(self::IS_IN_STOCK_KEY);
    }

    /**
     * GetQty
     *
     * @return float|null
     */
    public function getQty(): ?float
    {
        return $this->getData(self::QTY_KEY);
    }

    /**
     * GetSku
     *
     * @return string|null
     */
    public function getSku(): ?string
    {
        return $this->getData(self::SKU_KEY);
    }

    /**
     * GetType
     *
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->getData(self::TYPE_KEY);
    }

    /**
     * SetStoreData
     *
     * @param ProductStoreDataInterface[]|null $storeData
     *
     * @return ProductInterface
     */
    public function setStoreData(?array $storeData = null): ProductInterface
    {
        $this->setData(self::STORE_DATA_KEY, $storeData);

        return $this;
    }

    /**
     * SetCategories
     *
     * @param string[]|null $categories
     *
     * @return ProductInterface
     */
    public function setCategories(?array $categories = null): ProductInterface
    {
        $this->setData(self::CATEGORIES_KEY, $categories);

        return $this;
    }

    /**
     * SetChildrenEntityIds
     *
     * @param int[]|null $childrenEntityIds
     *
     * @return ProductInterface
     */
    public function setChildrenEntityIds(?array $childrenEntityIds = null): ProductInterface
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
     * @param ImagesInterface|null $images
     *
     * @return ProductInterface
     */
    public function setImages(?ImagesInterface $images = null): ProductInterface
    {
        $this->setData(self::IMAGES_KEY, $images);

        return $this;
    }

    /**
     * SetIsInStock
     *
     * @param int|null $isInStock
     *
     * @return ProductInterface
     */
    public function setIsInStock(?int $isInStock = null): ProductInterface
    {
        $this->setData(self::IS_IN_STOCK_KEY, $isInStock);

        return $this;
    }

    /**
     * SetQty
     *
     * @param float|null $qty
     *
     * @return ProductInterface
     */
    public function setQty(?float $qty = null): ProductInterface
    {
        $this->setData(self::QTY_KEY, $qty);

        return $this;
    }

    /**
     * SetSku
     *
     * @param string|null $sku
     *
     * @return ProductInterface
     */
    public function setSku(?string $sku = null): ProductInterface
    {
        $this->setData(self::SKU_KEY, $sku);

        return $this;
    }

    /**
     * SetType
     *
     * @param string|null $type
     *
     * @return ProductInterface
     */
    public function setType(?string $type = null): ProductInterface
    {
        $this->setData(self::TYPE_KEY, $type);

        return $this;
    }
}

<?php

namespace Emartech\Emarsys\Model\Data;

use Magento\Framework\DataObject;

use Emartech\Emarsys\Api\Data\ProductInterface;

/**
 * Class Product
 * @package Emartech\Emarsys\Model\Data
 */
class Product extends DataObject implements ProductInterface
{

    /**
     * @return \Emartech\Emarsys\Api\Data\ProductStoreDataInterface[]
     */
    public function getStoreData()
    {
        return $this->getData(self::STORE_DATA_KEY);
    }

    /**
     * @return mixed
     */
    public function getCategories()
    {
        return $this->getData(self::CATEGORIES_KEY);
    }

    /**
     * @return int[]
     */
    public function getChildrenEntityIds()
    {
        return $this->getData(self::CHILDREN_ENTITY_IDS_KEY);
    }

    /**
     * @return int
     */
    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID_KEY);
    }

    /**
     * @return \Emartech\Emarsys\Api\Data\ImagesInterface
     */
    public function getImages()
    {
        return $this->getData(self::IMAGES_KEY);
    }

    /**
     * @return int
     */
    public function getIsInStock()
    {
        return $this->getData(self::IS_IN_STOCK_KEY);
    }

    /**
     * @return float|int
     */
    public function getQty()
    {
        return $this->getData(self::QTY_KEY);
    }

    /**
     * @return string
     */
    public function getSku()
    {
        return $this->getData(self::SKU_KEY);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->getData(self::TYPE_KEY);
    }

    /**
     * @param \Emartech\Emarsys\Api\Data\ProductStoreDataInterface[] $storeData
     *
     * @return $this
     */
    public function setStoreData(array $storeData)
    {
        $this->setData(self::STORE_DATA_KEY, $storeData);

        return $this;
    }

    /**
     * @param mixed $categories
     *
     * @return $this
     */
    public function setCategories($categories)
    {
        $this->setData(self::CATEGORIES_KEY, $categories);

        return $this;
    }

    /**
     * @param int[] $childrenEntityIds
     *
     * @return $this
     */
    public function setChildrenEntityIds($childrenEntityIds)
    {
        $this->setData(self::CHILDREN_ENTITY_IDS_KEY, $childrenEntityIds);

        return $this;
    }

    /**
     * @param int $entityId
     *
     * @return $this
     */
    public function setEntityId($entityId)
    {
        $this->setData(self::ENTITY_ID_KEY, $entityId);

        return $this;
    }

    /**
     * @param \Emartech\Emarsys\Api\Data\ImagesInterface $images
     *
     * @return $this
     */
    public function setImages($images)
    {
        $this->setData(self::IMAGES_KEY, $images);

        return $this;
    }

    /**
     * @param int $isInStock
     *
     * @return $this
     */
    public function setIsInStock($isInStock)
    {
        $this->setData(self::IS_IN_STOCK_KEY, $isInStock);

        return $this;
    }

    /**
     * @param float|int $qty
     *
     * @return $this
     */
    public function setQty($qty)
    {
        $this->setData(self::QTY_KEY, $qty);

        return $this;
    }

    /**
     * @param string $sku
     *
     * @return $this
     */
    public function setSku($sku)
    {
        $this->setData(self::SKU_KEY, $sku);

        return $this;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->setData(self::TYPE_KEY, $type);

        return $this;
    }
}

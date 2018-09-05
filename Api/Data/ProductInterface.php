<?php

namespace Emartech\Emarsys\Api\Data;

/**
 * Interface ProductInterface
 * @package Emartech\Emarsys\Api\Data
 */
interface ProductInterface
{
    const ENTITY_ID_KEY           = 'entity_id';
    const TYPE_KEY                = 'type';
    const CHILDREN_ENTITY_IDS_KEY = 'children_entity_ids';
    const CATEGORIES_KEY          = 'categories';
    const SKU_KEY                 = 'sku';
    const IMAGES_KEY              = 'images';
    const QTY_KEY                 = 'qty';
    const IS_IN_STOCK_KEY         = 'is_in_stock';
    const STORE_DATA_KEY          = 'stores';

    const IMAGE_KEY               = 'image';
    const SMALL_IMAGE_KEY         = 'small_image';
    const THUMBNAIL_IMAGE_KEY     = 'thumbnail';

    /**
     * @return \Emartech\Emarsys\Api\Data\ProductStoreDataInterface[]
     */
    public function getStoreData();

    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return int[]
     */
    public function getChildrenEntityIds();

    /**
     * @return mixed
     */
    public function getCategories();

    /**
     * @return string
     */
    public function getSku();

    /**
     * @return \Emartech\Emarsys\Api\Data\ImagesInterface
     */
    public function getImages();

    /**
     * @return float|int
     */
    public function getQty();

    /**
     * @return int
     */
    public function getIsInStock();

    /**
     * @param \Emartech\Emarsys\Api\Data\ProductStoreDataInterface[] $storeData
     *
     * @return $this
     */
    public function setStoreData(array $storeData);

    /**
     * @param int $entityId
     *
     * @return $this
     */
    public function setEntityId($entityId);

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type);

    /**
     * @param int[] $childrenEntityIds
     *
     * @return $this
     */
    public function setChildrenEntityIds($childrenEntityIds);

    /**
     * @param mixed $categories
     *
     * @return $this
     */
    public function setCategories($categories);

    /**
     * @param string $sku
     *
     * @return $this
     */
    public function setSku($sku);

    /**
     * @param \Emartech\Emarsys\Api\Data\ImagesInterface $images
     *
     * @return $this
     */
    public function setImages($images);

    /**
     * @param float|int $qty
     *
     * @return $this
     */
    public function setQty($qty);

    /**
     * @param int $isInStock
     *
     * @return $this
     */
    public function setIsInStock($isInStock);
}

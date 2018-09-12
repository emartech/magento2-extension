<?php

namespace Emartech\Emarsys\Api\Data;

use Emartech\Emarsys\Api\Data\ImagesInterface;

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
    public function getStoreData(): array;

    /**
     * @return int
     */
    public function getEntityId(): int;

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @return int[]
     */
    public function getChildrenEntityIds(): array;

    /**
     * @return string[]
     */
    public function getCategories(): array;

    /**
     * @return string
     */
    public function getSku(): string;

    /**
     * @return \Emartech\Emarsys\Api\Data\ImagesInterface
     */
    public function getImages(): ImagesInterface;

    /**
     * @return float|int
     */
    public function getQty(): float;

    /**
     * @return int
     */
    public function getIsInStock(): int;

    /**
     * @param \Emartech\Emarsys\Api\Data\ProductStoreDataInterface[] $storeData
     *
     * @return $this
     */
    public function setStoreData(array $storeData): ProductInterface;

    /**
     * @param int $entityId
     *
     * @return $this
     */
    public function setEntityId($entityId): ProductInterface;

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type): ProductInterface;

    /**
     * @param int[] $childrenEntityIds
     *
     * @return $this
     */
    public function setChildrenEntityIds($childrenEntityIds): ProductInterface;

    /**
     * @param string[] $categories
     *
     * @return $this
     */
    public function setCategories($categories): ProductInterface;

    /**
     * @param string $sku
     *
     * @return $this
     */
    public function setSku($sku): ProductInterface;

    /**
     * @param \Emartech\Emarsys\Api\Data\ImagesInterface $images
     *
     * @return $this
     */
    public function setImages($images): ProductInterface;

    /**
     * @param float|int $qty
     *
     * @return $this
     */
    public function setQty($qty): ProductInterface;

    /**
     * @param int $isInStock
     *
     * @return $this
     */
    public function setIsInStock($isInStock): ProductInterface;
}

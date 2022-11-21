<?php

namespace Emartech\Emarsys\Api\Data;

interface ProductInterface
{
    public const ENTITY_ID_KEY           = 'entity_id';
    public const TYPE_KEY                = 'type';
    public const CHILDREN_ENTITY_IDS_KEY = 'children_entity_ids';
    public const CATEGORIES_KEY          = 'categories';
    public const SKU_KEY                 = 'sku';
    public const IMAGES_KEY              = 'images';
    public const QTY_KEY                 = 'qty';
    public const IS_IN_STOCK_KEY         = 'is_in_stock';
    public const STORE_DATA_KEY          = 'stores';

    public const IMAGE_KEY           = 'image';
    public const SMALL_IMAGE_KEY     = 'small_image';
    public const THUMBNAIL_IMAGE_KEY = 'thumbnail';

    /**
     * GetStoreData
     *
     * @return \Emartech\Emarsys\Api\Data\ProductStoreDataInterface[]
     */
    public function getStoreData(): array;

    /**
     * GetEntityId
     *
     * @return int
     */
    public function getEntityId(): int;

    /**
     * GetType
     *
     * @return string
     */
    public function getType(): string;

    /**
     * GetChildrenEntityIds
     *
     * @return int[]
     */
    public function getChildrenEntityIds(): array;

    /**
     * GetCategories
     *
     * @return string[]
     */
    public function getCategories(): array;

    /**
     * GetSku
     *
     * @return string
     */
    public function getSku(): string;

    /**
     * GetImages
     *
     * @return \Emartech\Emarsys\Api\Data\ImagesInterface
     */
    public function getImages(): ImagesInterface;

    /**
     * GetQty
     *
     * @return float
     */
    public function getQty(): float;

    /**
     * GetIsInStock
     *
     * @return int
     */
    public function getIsInStock(): int;

    /**
     * SetStoreData
     *
     * @param \Emartech\Emarsys\Api\Data\ProductStoreDataInterface[] $storeData
     *
     * @return \Emartech\Emarsys\Api\Data\ProductInterface
     */
    public function setStoreData(array $storeData): ProductInterface;

    /**
     * SetEntityId
     *
     * @param int $entityId
     *
     * @return \Emartech\Emarsys\Api\Data\ProductInterface
     */
    public function setEntityId(int $entityId): ProductInterface;

    /**
     * SetType
     *
     * @param string $type
     *
     * @return \Emartech\Emarsys\Api\Data\ProductInterface
     */
    public function setType(string $type): ProductInterface;

    /**
     * SetChildrenEntityIds
     *
     * @param int[] $childrenEntityIds
     *
     * @return \Emartech\Emarsys\Api\Data\ProductInterface
     */
    public function setChildrenEntityIds(array $childrenEntityIds): ProductInterface;

    /**
     * SetCategories
     *
     * @param string[] $categories
     *
     * @return \Emartech\Emarsys\Api\Data\ProductInterface
     */
    public function setCategories(array $categories): ProductInterface;

    /**
     * SetSku
     *
     * @param string $sku
     *
     * @return \Emartech\Emarsys\Api\Data\ProductInterface
     */
    public function setSku(string $sku): ProductInterface;

    /**
     * SetImages
     *
     * @param \Emartech\Emarsys\Api\Data\ImagesInterface $images
     *
     * @return \Emartech\Emarsys\Api\Data\ProductInterface
     */
    public function setImages(ImagesInterface $images): ProductInterface;

    /**
     * SetQty
     *
     * @param float $qty
     *
     * @return \Emartech\Emarsys\Api\Data\ProductInterface
     */
    public function setQty(float $qty): ProductInterface;

    /**
     * SetIsInStock
     *
     * @param int $isInStock
     *
     * @return \Emartech\Emarsys\Api\Data\ProductInterface
     */
    public function setIsInStock(int $isInStock): ProductInterface;
}

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
     * @return \Emartech\Emarsys\Api\Data\ProductStoreDataInterface[]|null
     */
    public function getStoreData(): ?array;

    /**
     * GetEntityId
     *
     * @return int
     */
    public function getEntityId(): int;

    /**
     * GetType
     *
     * @return string|null
     */
    public function getType(): ?string;

    /**
     * GetChildrenEntityIds
     *
     * @return int[]|null
     */
    public function getChildrenEntityIds(): ?array;

    /**
     * GetCategories
     *
     * @return string[]|null
     */
    public function getCategories(): ?array;

    /**
     * GetSku
     *
     * @return string|null
     */
    public function getSku(): ?string;

    /**
     * GetImages
     *
     * @return \Emartech\Emarsys\Api\Data\ImagesInterface|null
     */
    public function getImages(): ?ImagesInterface;

    /**
     * GetQty
     *
     * @return float|null
     */
    public function getQty(): ?float;

    /**
     * GetIsInStock
     *
     * @return int|null
     */
    public function getIsInStock(): ?int;

    /**
     * SetStoreData
     *
     * @param \Emartech\Emarsys\Api\Data\ProductStoreDataInterface[]|null $storeData
     *
     * @return \Emartech\Emarsys\Api\Data\ProductInterface
     */
    public function setStoreData(?array $storeData = null): ProductInterface;

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
     * @param string|null $type
     *
     * @return \Emartech\Emarsys\Api\Data\ProductInterface
     */
    public function setType(?string $type = null): ProductInterface;

    /**
     * SetChildrenEntityIds
     *
     * @param int[]|null $childrenEntityIds
     *
     * @return \Emartech\Emarsys\Api\Data\ProductInterface
     */
    public function setChildrenEntityIds(?array $childrenEntityIds = null): ProductInterface;

    /**
     * SetCategories
     *
     * @param string[]|null $categories
     *
     * @return \Emartech\Emarsys\Api\Data\ProductInterface
     */
    public function setCategories(?array $categories = null): ProductInterface;

    /**
     * SetSku
     *
     * @param string|null $sku
     *
     * @return \Emartech\Emarsys\Api\Data\ProductInterface
     */
    public function setSku(?string $sku = null): ProductInterface;

    /**
     * SetImages
     *
     * @param \Emartech\Emarsys\Api\Data\ImagesInterface|null $images
     *
     * @return \Emartech\Emarsys\Api\Data\ProductInterface
     */
    public function setImages(?ImagesInterface $images = null): ProductInterface;

    /**
     * SetQty
     *
     * @param float|null $qty
     *
     * @return \Emartech\Emarsys\Api\Data\ProductInterface
     */
    public function setQty(?float $qty = null): ProductInterface;

    /**
     * SetIsInStock
     *
     * @param int|null $isInStock
     *
     * @return \Emartech\Emarsys\Api\Data\ProductInterface
     */
    public function setIsInStock(?int $isInStock = null): ProductInterface;
}

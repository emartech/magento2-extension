<?php

namespace Emartech\Emarsys\Api\Data;

interface CategoryStoreDataInterface
{
    public const NAME_KEY        = 'name';
    public const IMAGE_KEY       = 'image';
    public const DESCRIPTION_KEY = 'description';
    public const IS_ACTIVE_KEY   = 'is_active';
    public const STORE_ID_KEY    = 'store_id';

    /**
     * GetName
     *
     * @return string
     */
    public function getName(): string;

    /**
     * GetImage
     *
     * @return string
     */
    public function getImage(): string;

    /**
     * GetDescription
     *
     * @return string
     */
    public function getDescription(): string;

    /**
     * GetIsActive
     *
     * @return int
     */
    public function getIsActive(): int;

    /**
     * GetStoreId
     *
     * @return int
     */
    public function getStoreId(): int;

    /**
     * SetName
     *
     * @param string $name
     *
     * @return \Emartech\Emarsys\Api\Data\CategoryStoreDataInterface
     */
    public function setName(string $name): CategoryStoreDataInterface;

    /**
     * SetImage
     *
     * @param string $image
     *
     * @return \Emartech\Emarsys\Api\Data\CategoryStoreDataInterface
     */
    public function setImage(string $image): CategoryStoreDataInterface;

    /**
     * SetDescription
     *
     * @param string $description
     *
     * @return \Emartech\Emarsys\Api\Data\CategoryStoreDataInterface
     */
    public function setDescription(string $description): CategoryStoreDataInterface;

    /**
     * SetIsActive
     *
     * @param int $isActive
     *
     * @return \Emartech\Emarsys\Api\Data\CategoryStoreDataInterface
     */
    public function setIsActive(int $isActive): CategoryStoreDataInterface;

    /**
     * SetStoreId
     *
     * @param int $storeId
     *
     * @return \Emartech\Emarsys\Api\Data\CategoryStoreDataInterface
     */
    public function setStoreId(int $storeId): CategoryStoreDataInterface;
}

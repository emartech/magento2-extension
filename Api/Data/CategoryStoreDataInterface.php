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
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * GetImage
     *
     * @return string|null
     */
    public function getImage(): ?string;

    /**
     * GetDescription
     *
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * GetIsActive
     *
     * @return int|null
     */
    public function getIsActive(): ?int;

    /**
     * GetStoreId
     *
     * @return int|null
     */
    public function getStoreId(): ?int;

    /**
     * SetName
     *
     * @param string|null $name
     *
     * @return \Emartech\Emarsys\Api\Data\CategoryStoreDataInterface
     */
    public function setName(?string $name = null): CategoryStoreDataInterface;

    /**
     * SetImage
     *
     * @param string|null $image
     *
     * @return \Emartech\Emarsys\Api\Data\CategoryStoreDataInterface
     */
    public function setImage(?string $image = null): CategoryStoreDataInterface;

    /**
     * SetDescription
     *
     * @param string|null $description
     *
     * @return \Emartech\Emarsys\Api\Data\CategoryStoreDataInterface
     */
    public function setDescription(?string $description = null): CategoryStoreDataInterface;

    /**
     * SetIsActive
     *
     * @param int|null $isActive
     *
     * @return \Emartech\Emarsys\Api\Data\CategoryStoreDataInterface
     */
    public function setIsActive(?int $isActive = null): CategoryStoreDataInterface;

    /**
     * SetStoreId
     *
     * @param int|null $storeId
     *
     * @return \Emartech\Emarsys\Api\Data\CategoryStoreDataInterface
     */
    public function setStoreId(?int $storeId = null): CategoryStoreDataInterface;
}

<?php

namespace Emartech\Emarsys\Api\Data;

/**
 * Interface ProductStoreDataInterface
 * @package Emartech\Emarsys\Api\Data
 */
interface CategoryStoreDataInterface
{
    const NAME_KEY        = 'name';
    const IMAGE_KEY       = 'image';
    const DESCRIPTION_KEY = 'description';
    const IS_ACTIVE_KEY   = 'is_active';
    const STORE_ID_KEY    = 'store_id';

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getImage(): string;

    /**
     * @return string
     */
    public function getDescription(): string;

    /**
     * @return int
     */
    public function getIsActive(): int;

    /**
     * @return int
     */
    public function getStoreId(): int;

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name): CategoryStoreDataInterface;

    /**
     * @param string $image
     *
     * @return $this
     */
    public function setImage($image): CategoryStoreDataInterface;

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description): CategoryStoreDataInterface;

    /**
     * @param int $isActive
     *
     * @return $this
     */
    public function setIsActive($isActive): CategoryStoreDataInterface;

    /**
     * @param int $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId): CategoryStoreDataInterface;
}

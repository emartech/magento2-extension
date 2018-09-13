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
    public function getName();

    /**
     * @return string
     */
    public function getImage();

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @return int
     */
    public function getIsActive();

    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name);

    /**
     * @param string $image
     *
     * @return $this
     */
    public function setImage($image);

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description);

    /**
     * @param int $isActive
     *
     * @return $this
     */
    public function setIsActive($isActive);

    /**
     * @param int $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId);
}

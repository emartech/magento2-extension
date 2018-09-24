<?php

namespace Emartech\Emarsys\Api\Data;

/**
 * Interface StoreConfigInterface
 * @package Emartech\Emarsys\Api\Data
 */
interface StoreConfigInterface
{
    const STORE_ID_KEY = 'store_id';
    const STORE_SLUG_KEY = 'slug';

    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @return string
     */
    public function getSlug();

    /**
     * @param int $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId);

    /**
     * @param string $slug
     *
     * @return $this
     */
    public function setSlug($slug);
}

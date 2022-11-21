<?php

namespace Emartech\Emarsys\Api\Data;

interface StoreConfigInterface
{
    public const STORE_ID_KEY   = 'store_id';
    public const STORE_SLUG_KEY = 'slug';

    /**
     * GetStoreId
     *
     * @return int
     */
    public function getStoreId(): int;

    /**
     * GetSlug
     *
     * @return string
     */
    public function getSlug(): string;

    /**
     * SetStoreId
     *
     * @param int $storeId
     *
     * @return \Emartech\Emarsys\Api\Data\StoreConfigInterface
     */
    public function setStoreId(int $storeId): StoreConfigInterface;

    /**
     * SetSlug
     *
     * @param string $slug
     *
     * @return \Emartech\Emarsys\Api\Data\StoreConfigInterface
     */
    public function setSlug(string $slug): StoreConfigInterface;
}

<?php

namespace Emartech\Emarsys\Api\Data;

interface StoreConfigInterface
{
    public const STORE_ID_KEY   = 'store_id';
    public const STORE_SLUG_KEY = 'slug';

    /**
     * GetStoreId
     *
     * @return int|null
     */
    public function getStoreId(): ?int;

    /**
     * GetSlug
     *
     * @return string|null
     */
    public function getSlug(): ?string;

    /**
     * SetStoreId
     *
     * @param int|null $storeId
     *
     * @return \Emartech\Emarsys\Api\Data\StoreConfigInterface
     */
    public function setStoreId(?int $storeId = null): StoreConfigInterface;

    /**
     * SetSlug
     *
     * @param string|null $slug
     *
     * @return \Emartech\Emarsys\Api\Data\StoreConfigInterface
     */
    public function setSlug(?string $slug = null): StoreConfigInterface;
}

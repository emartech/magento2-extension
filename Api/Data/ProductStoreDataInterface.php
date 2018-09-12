<?php

namespace Emartech\Emarsys\Api\Data;

/**
 * Interface ProductStoreDataInterface
 * @package Emartech\Emarsys\Api\Data
 */
interface ProductStoreDataInterface
{
    const NAME_KEY        = 'name';
    const PRICE_KEY       = 'price';
    const LINK_KEY        = 'url_key';
    const DESCRIPTION_KEY = 'description';
    const STATUS_KEY      = 'status';
    const STORE_ID_KEY    = 'store_id';

    const SPECIAL_PRICE_KEY     = 'special_price';
    const SPECIAL_FROM_DATE_KEY = 'special_from_date';
    const SPECIAL_TO_DATE_KEY   = 'special_to_date';

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return float
     */
    public function getPrice(): float;

    /**
     * @return string
     */
    public function getLink(): string;

    /**
     * @return string
     */
    public function getDescription(): string;

    /**
     * @return int
     */
    public function getStatus(): int;

    /**
     * @return int
     */
    public function getStoreId(): int;

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name): ProductStoreDataInterface;

    /**
     * @param float $price
     *
     * @return $this
     */
    public function setPrice($price): ProductStoreDataInterface;

    /**
     * @param string $link
     *
     * @return $this
     */
    public function setLink($link): ProductStoreDataInterface;

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description): ProductStoreDataInterface;

    /**
     * @param int $status
     *
     * @return $this
     */
    public function setStatus($status): ProductStoreDataInterface;

    /**
     * @param int $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId): ProductStoreDataInterface;
}

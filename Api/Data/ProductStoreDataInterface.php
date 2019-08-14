<?php

namespace Emartech\Emarsys\Api\Data;

/**
 * Interface ProductStoreDataInterface
 * @package Emartech\Emarsys\Api\Data
 */
interface ProductStoreDataInterface
{
    const NAME_KEY                   = 'name';
    const LINK_KEY                   = 'url_key';
    const DESCRIPTION_KEY            = 'description';
    const STATUS_KEY                 = 'status';
    const STORE_ID_KEY               = 'store_id';
    const CURRENCY_KEY               = 'currency';
    const PRICE_KEY                  = 'price';
    const DISPLAY_PRICE_KEY          = 'display_price';
    const ORIGINAL_PRICE_KEY         = 'original_price';
    const ORIGINAL_DISPLAY_PRICE_KEY = 'original_display_price';

    /**
     * @return string
     */
    public function getName();

    /**
     * @return float
     */
    public function getPrice();

    /**
     * @return float
     */
    public function getDisplayPrice();

    /**
     * @return float
     */
    public function getOriginalPrice();

    /**
     * @return float
     */
    public function getOriginalDisplayPrice();

    /**
     * @return string
     */
    public function getLink();

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @return string
     */
    public function getCurrencyCode();

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name);

    /**
     * @param float $price
     *
     * @return $this
     */
    public function setPrice($price);

    /**
     * @param float $displayPrice
     *
     * @return $this
     */
    public function setDisplayPrice($displayPrice);

    /**
     * @param float $originalPrice
     *
     * @return $this
     */
    public function setOriginalPrice($originalPrice);

    /**
     * @param float $originalDisplayPrice
     *
     * @return $this
     */
    public function setOriginalDisplayPrice($originalDisplayPrice);

    /**
     * @param string $link
     *
     * @return $this
     */
    public function setLink($link);

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description);

    /**
     * @param int $status
     *
     * @return $this
     */
    public function setStatus($status);

    /**
     * @param int $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId);

    /**
     * @param string $currencyCode
     *
     * @return $this
     */
    public function setCurrencyCode($currencyCode);
}

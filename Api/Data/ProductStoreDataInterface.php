<?php

namespace Emartech\Emarsys\Api\Data;

interface ProductStoreDataInterface
{
    const NAME_KEY                       = 'name';
    const LINK_KEY                       = 'url_key';
    const DESCRIPTION_KEY                = 'description';
    const STATUS_KEY                     = 'status';
    const STORE_ID_KEY                   = 'store_id';
    const CURRENCY_KEY                   = 'currency';
    const PRICE_KEY                      = 'price';
    const DISPLAY_PRICE_KEY              = 'display_price';
    const ORIGINAL_PRICE_KEY             = 'original_price';
    const ORIGINAL_DISPLAY_PRICE_KEY     = 'original_display_price';
    const WEBSHOP_PRICE                  = 'webshop_price';
    const DISPLAY_WEBSHOP_PRICE          = 'display_webshop_price';
    const ORIGINAL_WEBSHOP_PRICE         = 'original_webshop_price';
    const ORIGINAL_DISPLAY_WEBSHOP_PRICE = 'original_display_webshop_price';
    const EXTRA_FIELDS                   = 'extra_fields';
    const IMAGES_KEY                     = 'images';

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
     * @return float
     */
    public function getWebshopPrice();

    /**
     * @return float
     */
    public function getDisplayWebshopPrice();

    /**
     * @return float
     */
    public function getOriginalWebshopPrice();

    /**
     * @return float
     */
    public function getOriginalDisplayWebshopPrice();

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
     * @return \Emartech\Emarsys\Api\Data\ExtraFieldsInterface[]
     */
    public function getExtraFields();

    /**
     * @return \Emartech\Emarsys\Api\Data\ImagesInterface
     */
    public function getImages();

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
     * @param float $webShopPrice
     *
     * @return $this
     */
    public function setWebshopPrice($webShopPrice);

    /**
     * @param float $displayWebShopPrice
     *
     * @return $this
     */
    public function setDisplayWebshopPrice($displayWebShopPrice);

    /**
     * @param float $originalWebshopPrice
     *
     * @return $this
     */
    public function setOriginalWebshopPrice($originalWebshopPrice);

    /**
     * @param float $originalDisplayWebshopPrice
     *
     * @return $this
     */
    public function setOriginalDisplayWebshopPrice($originalDisplayWebshopPrice);

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

    /**
     * @param \Emartech\Emarsys\Api\Data\ExtraFieldsInterface[] $extraFields
     *
     * @return $this
     */
    public function setExtraFields($extraFields);

    /**
     * @param \Emartech\Emarsys\Api\Data\ImagesInterface $images
     *
     * @return $this
     */
    public function setImages($images);
}

<?php

namespace Emartech\Emarsys\Api\Data;

interface ProductStoreDataInterface
{
    public const NAME_KEY                       = 'name';
    public const LINK_KEY                       = 'url_key';
    public const DESCRIPTION_KEY                = 'description';
    public const STATUS_KEY                     = 'status';
    public const STORE_ID_KEY                   = 'store_id';
    public const CURRENCY_KEY                   = 'currency';
    public const PRICE_KEY                      = 'price';
    public const DISPLAY_PRICE_KEY              = 'display_price';
    public const ORIGINAL_PRICE_KEY             = 'original_price';
    public const ORIGINAL_DISPLAY_PRICE_KEY     = 'original_display_price';
    public const WEBSHOP_PRICE                  = 'webshop_price';
    public const DISPLAY_WEBSHOP_PRICE          = 'display_webshop_price';
    public const ORIGINAL_WEBSHOP_PRICE         = 'original_webshop_price';
    public const ORIGINAL_DISPLAY_WEBSHOP_PRICE = 'original_display_webshop_price';
    public const EXTRA_FIELDS                   = 'extra_fields';
    public const IMAGES_KEY                     = 'images';

    /**
     * GetName
     *
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * GetPrice
     *
     * @return float|null
     */
    public function getPrice(): ?float;

    /**
     * GetDisplayPrice
     *
     * @return float|null
     */
    public function getDisplayPrice(): ?float;

    /**
     * GetOriginalPrice
     *
     * @return float|null
     */
    public function getOriginalPrice(): ?float;

    /**
     * GetOriginalDisplayPrice
     *
     * @return float|null
     */
    public function getOriginalDisplayPrice(): ?float;

    /**
     * GetWebshopPrice
     *
     * @return float|null
     */
    public function getWebshopPrice(): ?float;

    /**
     * GetDisplayWebshopPrice
     *
     * @return float|null
     */
    public function getDisplayWebshopPrice(): ?float;

    /**
     * GetOriginalWebshopPrice
     *
     * @return float|null
     */
    public function getOriginalWebshopPrice(): ?float;

    /**
     * GetOriginalDisplayWebshopPrice
     *
     * @return float|null
     */
    public function getOriginalDisplayWebshopPrice(): ?float;

    /**
     * GetLink
     *
     * @return string|null
     */
    public function getLink(): ?string;

    /**
     * GetDescription
     *
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * GetStatus
     *
     * @return int|null
     */
    public function getStatus(): ?int;

    /**
     * GetStoreId
     *
     * @return int|null
     */
    public function getStoreId(): ?int;

    /**
     * GetCurrencyCode
     *
     * @return string|null
     */
    public function getCurrencyCode(): ?string;

    /**
     * GetExtraFields
     *
     * @return \Emartech\Emarsys\Api\Data\ExtraFieldsInterface[]|null
     */
    public function getExtraFields(): ?array;

    /**
     * GetImages
     *
     * @return \Emartech\Emarsys\Api\Data\ImagesInterface|null
     */
    public function getImages(): ?ImagesInterface;

    /**
     * SetName
     *
     * @param string|null $name
     *
     * @return \Emartech\Emarsys\Api\Data\ProductStoreDataInterface
     */
    public function setName(?string $name = null): ProductStoreDataInterface;

    /**
     * SetPrice
     *
     * @param float|null $price
     *
     * @return \Emartech\Emarsys\Api\Data\ProductStoreDataInterface
     */
    public function setPrice(?float $price = null): ProductStoreDataInterface;

    /**
     * SetDisplayPrice
     *
     * @param float|null $displayPrice
     *
     * @return \Emartech\Emarsys\Api\Data\ProductStoreDataInterface
     */
    public function setDisplayPrice(?float $displayPrice = null): ProductStoreDataInterface;

    /**
     * SetOriginalPrice
     *
     * @param float|null $originalPrice
     *
     * @return \Emartech\Emarsys\Api\Data\ProductStoreDataInterface
     */
    public function setOriginalPrice(?float $originalPrice = null): ProductStoreDataInterface;

    /**
     * SetOriginalDisplayPrice
     *
     * @param float|null $originalDisplayPrice
     *
     * @return \Emartech\Emarsys\Api\Data\ProductStoreDataInterface
     */
    public function setOriginalDisplayPrice(?float $originalDisplayPrice = null): ProductStoreDataInterface;

    /**
     * SetWebshopPrice
     *
     * @param float|null $webShopPrice
     *
     * @return \Emartech\Emarsys\Api\Data\ProductStoreDataInterface
     */
    public function setWebshopPrice(?float $webShopPrice = null): ProductStoreDataInterface;

    /**
     * SetDisplayWebshopPrice
     *
     * @param float|null $displayWebShopPrice
     *
     * @return \Emartech\Emarsys\Api\Data\ProductStoreDataInterface
     */
    public function setDisplayWebshopPrice(?float $displayWebShopPrice = null): ProductStoreDataInterface;

    /**
     * SetOriginalWebshopPrice
     *
     * @param float|null $originalWebshopPrice
     *
     * @return \Emartech\Emarsys\Api\Data\ProductStoreDataInterface
     */
    public function setOriginalWebshopPrice(?float $originalWebshopPrice = null): ProductStoreDataInterface;

    /**
     * SetOriginalDisplayWebshopPrice
     *
     * @param float|null $originalDisplayWebshopPrice
     *
     * @return \Emartech\Emarsys\Api\Data\ProductStoreDataInterface
     */
    public function setOriginalDisplayWebshopPrice(
        ?float $originalDisplayWebshopPrice = null
    ): ProductStoreDataInterface;

    /**
     * SetLink
     *
     * @param string|null $link
     *
     * @return \Emartech\Emarsys\Api\Data\ProductStoreDataInterface
     */
    public function setLink(?string $link = null): ProductStoreDataInterface;

    /**
     * SetDescription
     *
     * @param string|null $description
     *
     * @return \Emartech\Emarsys\Api\Data\ProductStoreDataInterface
     */
    public function setDescription(?string $description = null): ProductStoreDataInterface;

    /**
     * SetStatus
     *
     * @param int|null $status
     *
     * @return \Emartech\Emarsys\Api\Data\ProductStoreDataInterface
     */
    public function setStatus(?int $status = null): ProductStoreDataInterface;

    /**
     * SetStoreId
     *
     * @param int|null $storeId
     *
     * @return \Emartech\Emarsys\Api\Data\ProductStoreDataInterface
     */
    public function setStoreId(?int $storeId = null): ProductStoreDataInterface;

    /**
     * SetCurrencyCode
     *
     * @param string|null $currencyCode
     *
     * @return \Emartech\Emarsys\Api\Data\ProductStoreDataInterface
     */
    public function setCurrencyCode(?string $currencyCode = null): ProductStoreDataInterface;

    /**
     * SetExtraFields
     *
     * @param \Emartech\Emarsys\Api\Data\ExtraFieldsInterface[]|null $extraFields
     *
     * @return \Emartech\Emarsys\Api\Data\ProductStoreDataInterface
     */
    public function setExtraFields(?array $extraFields = null): ProductStoreDataInterface;

    /**
     * SetImages
     *
     * @param \Emartech\Emarsys\Api\Data\ImagesInterface|null $images
     *
     * @return \Emartech\Emarsys\Api\Data\ProductStoreDataInterface
     */
    public function setImages(?ImagesInterface $images = null): ProductStoreDataInterface;
}

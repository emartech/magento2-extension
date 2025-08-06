<?php

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\ExtraFieldsInterface;
use Emartech\Emarsys\Api\Data\ImagesInterface;
use Emartech\Emarsys\Api\Data\ProductStoreDataInterface;
use Magento\Framework\DataObject;

class ProductStoreData extends DataObject implements ProductStoreDataInterface
{
    /**
     * GetDescription
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return (string) $this->getData(self::DESCRIPTION_KEY);
    }

    /**
     * GetLink
     *
     * @return string|null
     */
    public function getLink(): ?string
    {
        return (string) $this->getData(self::LINK_KEY);
    }

    /**
     * GetName
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->getData(self::NAME_KEY);
    }

    /**
     * GetPrice
     *
     * @return float|null
     */
    public function getPrice(): ?float
    {
        return $this->getData(self::PRICE_KEY);
    }

    /**
     * GetDisplayPrice
     *
     * @return float|null
     */
    public function getDisplayPrice(): ?float
    {
        return $this->getData(self::DISPLAY_PRICE_KEY);
    }

    /**
     * GetOriginalPrice
     *
     * @return float|null
     */
    public function getOriginalPrice(): ?float
    {
        return $this->getData(self::ORIGINAL_PRICE_KEY);
    }

    /**
     * GetOriginalDisplayPrice
     *
     * @return float|null
     */
    public function getOriginalDisplayPrice(): ?float
    {
        return $this->getData(self::ORIGINAL_DISPLAY_PRICE_KEY);
    }

    /**
     * GetWebshopPrice
     *
     * @return float|null
     */
    public function getWebshopPrice(): ?float
    {
        return $this->getData(self::WEBSHOP_PRICE);
    }

    /**
     * GetDisplayWebshopPrice
     *
     * @return float|null
     */
    public function getDisplayWebshopPrice(): ?float
    {
        return $this->getData(self::DISPLAY_WEBSHOP_PRICE);
    }

    /**
     * GetOriginalWebshopPrice
     *
     * @return float|null
     */
    public function getOriginalWebshopPrice(): ?float
    {
        return $this->getData(self::ORIGINAL_WEBSHOP_PRICE);
    }

    /**
     * GetOriginalDisplayWebshopPrice
     *
     * @return float|null
     */
    public function getOriginalDisplayWebshopPrice(): ?float
    {
        return $this->getData(self::ORIGINAL_DISPLAY_WEBSHOP_PRICE);
    }

    /**
     * GetStatus
     *
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return $this->getData(self::STATUS_KEY);
    }

    /**
     * GetStoreId
     *
     * @return int|null
     */
    public function getStoreId(): ?int
    {
        return $this->getData(self::STORE_ID_KEY);
    }

    /**
     * GetCurrencyCode
     *
     * @return string|null
     */
    public function getCurrencyCode(): ?string
    {
        return $this->getData(self::CURRENCY_KEY);
    }

    /**
     * GetExtraFields
     *
     * @return ExtraFieldsInterface[]|null
     */
    public function getExtraFields(): ?array
    {
        return $this->getData(self::EXTRA_FIELDS);
    }

    /**
     * GetImages
     *
     * @return ImagesInterface|null
     */
    public function getImages(): ?ImagesInterface
    {
        return $this->getData(self::IMAGES_KEY);
    }

    /**
     * SetDescription
     *
     * @param string|null $description
     *
     * @return ProductStoreDataInterface
     */
    public function setDescription(?string $description = null): ProductStoreDataInterface
    {
        $this->setData(self::DESCRIPTION_KEY, $description);

        return $this;
    }

    /**
     * SetLink
     *
     * @param string|null $link
     *
     * @return ProductStoreDataInterface
     */
    public function setLink(?string $link = null): ProductStoreDataInterface
    {
        $this->setData(self::LINK_KEY, $link);

        return $this;
    }

    /**
     * SetName
     *
     * @param string|null $name
     *
     * @return ProductStoreDataInterface
     */
    public function setName(?string $name = null): ProductStoreDataInterface
    {
        $this->setData(self::NAME_KEY, $name);

        return $this;
    }

    /**
     * SetPrice
     *
     * @param float|null $price
     *
     * @return ProductStoreDataInterface
     */
    public function setPrice(?float $price = null): ProductStoreDataInterface
    {
        $this->setData(self::PRICE_KEY, $price);

        return $this;
    }

    /**
     * SetDisplayPrice
     *
     * @param float|null $displayPrice
     *
     * @return ProductStoreDataInterface
     */
    public function setDisplayPrice(?float $displayPrice = null): ProductStoreDataInterface
    {
        $this->setData(self::DISPLAY_PRICE_KEY, $displayPrice);

        return $this;
    }

    /**
     * SetOriginalPrice
     *
     * @param float|null $originalPrice
     *
     * @return ProductStoreDataInterface
     */
    public function setOriginalPrice(?float $originalPrice = null): ProductStoreDataInterface
    {
        $this->setData(self::ORIGINAL_PRICE_KEY, $originalPrice);

        return $this;
    }

    /**
     * SetOriginalDisplayPrice
     *
     * @param float|null $originalDisplayPrice
     *
     * @return ProductStoreDataInterface
     */
    public function setOriginalDisplayPrice(?float $originalDisplayPrice = null): ProductStoreDataInterface
    {
        $this->setData(self::ORIGINAL_DISPLAY_PRICE_KEY, $originalDisplayPrice);

        return $this;
    }

    /**
     * SetWebshopPrice
     *
     * @param float|null $webShopPrice
     *
     * @return ProductStoreDataInterface
     */
    public function setWebshopPrice(?float $webShopPrice = null): ProductStoreDataInterface
    {
        $this->setData(self::WEBSHOP_PRICE, $webShopPrice);

        return $this;
    }

    /**
     * SetDisplayWebshopPrice
     *
     * @param float|null $displayWebShopPrice
     *
     * @return ProductStoreDataInterface
     */
    public function setDisplayWebshopPrice(?float $displayWebShopPrice = null): ProductStoreDataInterface
    {
        $this->setData(self::DISPLAY_WEBSHOP_PRICE, $displayWebShopPrice);

        return $this;
    }

    /**
     * SetOriginalWebshopPrice
     *
     * @param float|null $originalWebshopPrice
     *
     * @return ProductStoreDataInterface
     */
    public function setOriginalWebshopPrice(?float $originalWebshopPrice = null): ProductStoreDataInterface
    {
        $this->setData(self::ORIGINAL_WEBSHOP_PRICE, $originalWebshopPrice);

        return $this;
    }

    /**
     * SetOriginalDisplayWebshopPrice
     *
     * @param float|null $originalDisplayWebshopPrice
     *
     * @return ProductStoreDataInterface
     */
    public function setOriginalDisplayWebshopPrice(
        ?float $originalDisplayWebshopPrice = null
    ): ProductStoreDataInterface {
        $this->setData(self::ORIGINAL_DISPLAY_WEBSHOP_PRICE, $originalDisplayWebshopPrice);

        return $this;
    }

    /**
     * SetStatus
     *
     * @param int|null $status
     *
     * @return ProductStoreDataInterface
     */
    public function setStatus(?int $status = null): ProductStoreDataInterface
    {
        $this->setData(self::STATUS_KEY, $status);

        return $this;
    }

    /**
     * SetStoreId
     *
     * @param int|null $storeId
     *
     * @return ProductStoreDataInterface
     */
    public function setStoreId(?int $storeId = null): ProductStoreDataInterface
    {
        $this->setData(self::STORE_ID_KEY, $storeId);

        return $this;
    }

    /**
     * SetCurrencyCode
     *
     * @param string|null $currencyCode
     *
     * @return ProductStoreDataInterface
     */
    public function setCurrencyCode(?string $currencyCode = null): ProductStoreDataInterface
    {
        $this->setData(self::CURRENCY_KEY, $currencyCode);

        return $this;
    }

    /**
     * SetExtraFields
     *
     * @param ExtraFieldsInterface[]|null $extraFields
     *
     * @return ProductStoreDataInterface
     */
    public function setExtraFields(?array $extraFields = null): ProductStoreDataInterface
    {
        $this->setData(self::EXTRA_FIELDS, $extraFields);

        return $this;
    }

    /**
     * SetImages
     *
     * @param ImagesInterface|null $images
     *
     * @return ProductStoreDataInterface
     */
    public function setImages(?ImagesInterface $images = null): ProductStoreDataInterface
    {
        $this->setData(self::IMAGES_KEY, $images);

        return $this;
    }
}

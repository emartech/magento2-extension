<?php

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\ExtraFieldsInterface;
use Emartech\Emarsys\Api\Data\ImagesInterface;
use Emartech\Emarsys\Api\Data\ProductStoreDataInterface;
use Magento\Framework\DataObject;

class ProductStoreData extends DataObject implements ProductStoreDataInterface
{
    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION_KEY);
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->getData(self::LINK_KEY);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getData(self::NAME_KEY);
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->getData(self::PRICE_KEY);
    }

    /**
     * @return float
     */
    public function getDisplayPrice()
    {
        return $this->getData(self::DISPLAY_PRICE_KEY);
    }

    /**
     * @return float
     */
    public function getOriginalPrice()
    {
        return $this->getData(self::ORIGINAL_PRICE_KEY);
    }

    /**
     * @return float
     */
    public function getOriginalDisplayPrice()
    {
        return $this->getData(self::ORIGINAL_DISPLAY_PRICE_KEY);
    }

    /**
     * @return float
     */
    public function getWebshopPrice()
    {
        return $this->getData(self::WEBSHOP_PRICE);
    }

    /**
     * @return float
     */
    public function getDisplayWebshopPrice()
    {
        return $this->getData(self::DISPLAY_WEBSHOP_PRICE);
    }

    /**
     * @return float
     */
    public function getOriginalWebshopPrice()
    {
        return $this->getData(self::ORIGINAL_WEBSHOP_PRICE);
    }

    /**
     * @return float
     */
    public function getOriginalDisplayWebshopPrice()
    {
        return $this->getData(self::ORIGINAL_DISPLAY_WEBSHOP_PRICE);
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS_KEY);
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID_KEY);
    }

    /**
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->getData(self::CURRENCY_KEY);
    }

    /**
     * @return ExtraFieldsInterface[]
     */
    public function getExtraFields()
    {
        return $this->getData(self::EXTRA_FIELDS);
    }

    /**
     * @return ImagesInterface
     */
    public function getImages()
    {
        return $this->getData(self::IMAGES_KEY);
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->setData(self::DESCRIPTION_KEY, $description);

        return $this;
    }

    /**
     * @param string $link
     *
     * @return $this
     */
    public function setLink($link)
    {
        $this->setData(self::LINK_KEY, $link);

        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->setData(self::NAME_KEY, $name);

        return $this;
    }

    /**
     * @param float $price
     *
     * @return $this
     */
    public function setPrice($price)
    {
        $this->setData(self::PRICE_KEY, $price);

        return $this;
    }

    /**
     * @param float $displayPrice
     *
     * @return $this
     */
    public function setDisplayPrice($displayPrice)
    {
        $this->setData(self::DISPLAY_PRICE_KEY, $displayPrice);

        return $this;
    }

    /**
     * @param float $originalPrice
     *
     * @return $this
     */
    public function setOriginalPrice($originalPrice)
    {
        $this->setData(self::ORIGINAL_PRICE_KEY, $originalPrice);

        return $this;
    }

    /**
     * @param float $originalDisplayPrice
     *
     * @return $this
     */
    public function setOriginalDisplayPrice($originalDisplayPrice)
    {
        $this->setData(self::ORIGINAL_DISPLAY_PRICE_KEY, $originalDisplayPrice);

        return $this;
    }

    /**
     * @param float $webShopPrice
     *
     * @return $this
     */
    public function setWebshopPrice($webShopPrice)
    {
        $this->setData(self::WEBSHOP_PRICE, $webShopPrice);

        return $this;
    }

    /**
     * @param float $displayWebShopPrice
     *
     * @return $this
     */
    public function setDisplayWebshopPrice($displayWebShopPrice)
    {
        $this->setData(self::DISPLAY_WEBSHOP_PRICE, $displayWebShopPrice);

        return $this;
    }

    /**
     * @param float $originalWebshopPrice
     *
     * @return $this
     */
    public function setOriginalWebshopPrice($originalWebshopPrice)
    {
        $this->setData(self::ORIGINAL_WEBSHOP_PRICE, $originalWebshopPrice);

        return $this;
    }

    /**
     * @param float $originalDisplayWebshopPrice
     *
     * @return $this
     */
    public function setOriginalDisplayWebshopPrice($originalDisplayWebshopPrice)
    {
        $this->setData(self::ORIGINAL_DISPLAY_WEBSHOP_PRICE, $originalDisplayWebshopPrice);

        return $this;
    }

    /**
     * @param int $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->setData(self::STATUS_KEY, $status);

        return $this;
    }

    /**
     * @param int $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->setData(self::STORE_ID_KEY, $storeId);

        return $this;
    }

    /**
     * @param string $currencyCode
     *
     * @return $this
     */
    public function setCurrencyCode($currencyCode)
    {
        $this->setData(self::CURRENCY_KEY, $currencyCode);

        return $this;
    }

    /**
     * @param ExtraFieldsInterface[] $extraFields
     *
     * @return $this
     */
    public function setExtraFields($extraFields)
    {
        $this->setData(self::EXTRA_FIELDS, $extraFields);
        return $this;
    }

    /**
     * @param ImagesInterface $images
     *
     * @return $this
     */
    public function setImages($images)
    {
        $this->setData(self::IMAGES_KEY, $images);
        return $this;
    }
}

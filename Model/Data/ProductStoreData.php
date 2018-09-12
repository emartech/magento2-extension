<?php

namespace Emartech\Emarsys\Model\Data;

use Magento\Framework\DataObject;

use Emartech\Emarsys\Api\Data\ProductStoreDataInterface;

/**
 * Class Product
 * @package Emartech\Emarsys\Model\Data
 */
class ProductStoreData extends DataObject implements ProductStoreDataInterface
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->getData(self::DESCRIPTION_KEY);
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->getData(self::LINK_KEY);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->getData(self::NAME_KEY);
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->getData(self::PRICE_KEY);
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->getData(self::STATUS_KEY);
    }

    /**
     * @return int
     */
    public function getStoreId(): int
    {
        return $this->getData(self::STORE_ID_KEY);
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description): ProductStoreDataInterface
    {
        $this->setData(self::DESCRIPTION_KEY, $description);

        return $this;
    }

    /**
     * @param string $link
     *
     * @return $this
     */
    public function setLink($link): ProductStoreDataInterface
    {
        $this->setData(self::LINK_KEY, $link);

        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name): ProductStoreDataInterface
    {
        $this->setData(self::NAME_KEY, $name);

        return $this;
    }

    /**
     * @param float $price
     *
     * @return $this
     */
    public function setPrice($price): ProductStoreDataInterface
    {
        $this->setData(self::PRICE_KEY, $price);

        return $this;
    }

    /**
     * @param int $status
     *
     * @return $this
     */
    public function setStatus($status): ProductStoreDataInterface
    {
        $this->setData(self::STATUS_KEY, $status);

        return $this;
    }

    /**
     * @param int $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId): ProductStoreDataInterface
    {
        $this->setData(self::STORE_ID_KEY, $storeId);

        return $this;
    }
}

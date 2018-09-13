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
     * @return string|null
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
}

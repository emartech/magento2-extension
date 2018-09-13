<?php

namespace Emartech\Emarsys\Model\Data;

use Magento\Framework\DataObject;

use Emartech\Emarsys\Api\Data\CategoryStoreDataInterface;

/**
 * Class CategoryStoreData
 * @package Emartech\Emarsys\Model\Data
 */
class CategoryStoreData extends DataObject implements CategoryStoreDataInterface
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
    public function getName()
    {
        return $this->getData(self::NAME_KEY);
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->getData(self::IMAGE_KEY);
    }

    /**
     * @return int
     */
    public function getIsActive()
    {
        return $this->getData(self::IS_ACTIVE_KEY);
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
     * @param string $image
     *
     * @return $this
     */
    public function setImage($image)
    {
        $this->setData(self::IMAGE_KEY, $image);

        return $this;
    }

    /**
     * @param int $isActive
     *
     * @return $this
     */
    public function setIsActive($isActive)
    {
        $this->setData(self::IS_ACTIVE_KEY, $isActive);

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

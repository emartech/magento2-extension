<?php

namespace Emartech\Emarsys\Model\Data;

use Magento\Framework\DataObject;

use Emartech\Emarsys\Api\Data\StoreConfigInterface;

/**
 * Class StoreConfig
 * @package Emartech\Emarsys\Model\Data
 */
class StoreConfig extends DataObject implements StoreConfigInterface
{
    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->getData(self::STORE_SLUG_KEY);
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_SLUG_KEY);
    }

    /**
     * @param string $slug
     *
     * @return $this
     */
    public function setSlug($slug)
    {
        $this->setData(self::STORE_SLUG_KEY, $slug);

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

<?php

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\StoreConfigInterface;
use Magento\Framework\DataObject;

class StoreConfig extends DataObject implements StoreConfigInterface
{
    /**
     * GetSlug
     *
     * @return string
     */
    public function getSlug(): string
    {
        return (string) $this->getData(self::STORE_SLUG_KEY);
    }

    /**
     * GetStoreId
     *
     * @return int
     */
    public function getStoreId(): int
    {
        return (int) $this->getData(self::STORE_SLUG_KEY);
    }

    /**
     * SetSlug
     *
     * @param string $slug
     *
     * @return StoreConfigInterface
     */
    public function setSlug(string $slug): StoreConfigInterface
    {
        $this->setData(self::STORE_SLUG_KEY, $slug);

        return $this;
    }

    /**
     * SetStoreId
     *
     * @param int $storeId
     *
     * @return StoreConfigInterface
     */
    public function setStoreId(int $storeId): StoreConfigInterface
    {
        $this->setData(self::STORE_ID_KEY, $storeId);

        return $this;
    }
}

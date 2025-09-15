<?php

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\StoreConfigInterface;
use Magento\Framework\DataObject;

class StoreConfig extends DataObject implements StoreConfigInterface
{
    /**
     * GetSlug
     *
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->getData(self::STORE_SLUG_KEY);
    }

    /**
     * GetStoreId
     *
     * @return int|null
     */
    public function getStoreId(): ?int
    {
        return $this->getData(self::STORE_SLUG_KEY);
    }

    /**
     * SetSlug
     *
     * @param string|null $slug
     *
     * @return StoreConfigInterface
     */
    public function setSlug(?string $slug = null): StoreConfigInterface
    {
        $this->setData(self::STORE_SLUG_KEY, $slug);

        return $this;
    }

    /**
     * SetStoreId
     *
     * @param int|null $storeId
     *
     * @return StoreConfigInterface
     */
    public function setStoreId(?int $storeId = null): StoreConfigInterface
    {
        $this->setData(self::STORE_ID_KEY, $storeId);

        return $this;
    }
}

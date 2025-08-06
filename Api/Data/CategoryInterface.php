<?php

namespace Emartech\Emarsys\Api\Data;

interface CategoryInterface
{
    public const ENTITY_ID_KEY      = 'entity_id';
    public const PATH_KEY           = 'path';
    public const CHILDREN_COUNT_KEY = 'children_count';
    public const STORE_DATA_KEY     = 'stores';

    /**
     * GetStoreData
     *
     * @return \Emartech\Emarsys\Api\Data\CategoryStoreDataInterface[]|null
     */
    public function getStoreData(): ?array;

    /**
     * GetEntityId
     *
     * @return int
     */
    public function getEntityId(): int;

    /**
     * GetPath
     *
     * @return string|null
     */
    public function getPath(): ?string;

    /**
     * GetChildrenCount
     *
     * @return int|null
     */
    public function getChildrenCount(): ?int;

    /**
     * SetEntityId
     *
     * @param int $entityId
     *
     * @return \Emartech\Emarsys\Api\Data\CategoryInterface
     */
    public function setEntityId(int $entityId): CategoryInterface;

    /**
     * SetPath
     *
     * @param string|null $path
     *
     * @return \Emartech\Emarsys\Api\Data\CategoryInterface
     */
    public function setPath(?string $path = null): CategoryInterface;

    /**
     * SetChildrenCount
     *
     * @param int|null $childrenCount
     *
     * @return \Emartech\Emarsys\Api\Data\CategoryInterface
     */
    public function setChildrenCount(?int $childrenCount = null): CategoryInterface;

    /**
     * SetStoreData
     *
     * @param \Emartech\Emarsys\Api\Data\CategoryStoreDataInterface[]|null $storeData
     *
     * @return \Emartech\Emarsys\Api\Data\CategoryInterface
     */
    public function setStoreData(?array $storeData = null): CategoryInterface;
}

<?php

namespace Emartech\Emarsys\Api\Data;

interface CategoryInterface
{
    const ENTITY_ID_KEY      = 'entity_id';
    const PATH_KEY           = 'path';
    const CHILDREN_COUNT_KEY = 'children_count';
    const STORE_DATA_KEY     = 'stores';

    /**
     * @return \Emartech\Emarsys\Api\Data\CategoryStoreDataInterface[]
     */
    public function getStoreData(): array;

    /**
     * @return int
     */
    public function getEntityId(): int;

    /**
     * @return string
     */
    public function getPath(): string;

    /**
     * @return int
     */
    public function getChildrenCount(): int;

    /**
     * @param int $entityId
     *
     * @return $this
     */
    public function setEntityId($entityId): CategoryInterface;

    /**
     * @param string $path
     *
     * @return $this
     */
    public function setPath($path): CategoryInterface;

    /**
     * @param int $childrenCount
     *
     * @return $this
     */
    public function setChildrenCount($childrenCount): CategoryInterface;

    /**
     * @param \Emartech\Emarsys\Api\Data\CategoryStoreDataInterface[] $storeData
     *
     * @return $this
     */
    public function setStoreData($storeData): CategoryInterface;
}

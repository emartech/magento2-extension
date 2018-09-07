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
    public function getStoreData();

    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @return string
     */
    public function getPath();

    /**
     * @return int
     */
    public function getChildrenCount();

    /**
     * @param int $entityId
     *
     * @return $this
     */
    public function setEntityId($entityId);

    /**
     * @param string $path
     *
     * @return $this
     */
    public function setPath($path);

    /**
     * @param int $childrenCount
     *
     * @return $this
     */
    public function setChildrenCount($childrenCount);

    /**
     * @param \Emartech\Emarsys\Api\Data\CategoryStoreDataInterface[] $storeData
     *
     * @return $this
     */
    public function setStoreData($storeData);
}

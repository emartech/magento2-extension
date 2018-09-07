<?php

namespace Emartech\Emarsys\Api\Data;

/**
 * Interface CategoriesApiResponseInterface
 * @package Emartech\Emarsys\Api\Data
 */
interface CategoriesApiResponseInterface extends ListApiResponseBaseInterface
{
    const CATEGORIES_KEY = 'categories';

    /**
     * @return \Emartech\Emarsys\Api\Data\CategoryInterface[]
     */
    public function getCategories();

    /**
     * @param \Emartech\Emarsys\Api\Data\CategoryInterface[] $categories
     *
     * @return $this
     */
    public function setCategories(array $categories);
}

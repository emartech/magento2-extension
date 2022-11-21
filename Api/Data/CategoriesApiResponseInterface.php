<?php

namespace Emartech\Emarsys\Api\Data;

interface CategoriesApiResponseInterface extends ListApiResponseBaseInterface
{
    public const CATEGORIES_KEY = 'categories';

    /**
     * GetCategories
     *
     * @return \Emartech\Emarsys\Api\Data\CategoryInterface[]
     */
    public function getCategories(): array;

    /**
     * SetCategories
     *
     * @param \Emartech\Emarsys\Api\Data\CategoryInterface[] $categories
     *
     * @return \Emartech\Emarsys\Api\Data\CategoriesApiResponseInterface
     */
    public function setCategories(array $categories): CategoriesApiResponseInterface;
}

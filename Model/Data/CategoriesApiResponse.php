<?php

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\CategoriesApiResponseInterface;
use Emartech\Emarsys\Api\Data\CategoryInterface;

class CategoriesApiResponse extends ListApiResponseBase implements CategoriesApiResponseInterface
{
    /**
     * GetCategories
     *
     * @return CategoryInterface[]
     */
    public function getCategories(): array
    {
        return $this->getData(self::CATEGORIES_KEY);
    }

    /**
     * SetCategories
     *
     * @param CategoryInterface[] $categories
     *
     * @return CategoriesApiResponseInterface
     */
    public function setCategories(array $categories): CategoriesApiResponseInterface
    {
        $this->setData(self::CATEGORIES_KEY, $categories);

        return $this;
    }
}

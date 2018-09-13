<?php

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\CategoriesApiResponseInterface;
use Emartech\Emarsys\Api\Data\CategoryInterface;

/**
 * Class ProductsApiResponse
 * @package Emartech\Emarsys\Model\Data
 */
class CategoriesApiResponse extends ListApiResponseBase implements CategoriesApiResponseInterface
{
    /**
     * @return CategoryInterface[]
     */
    public function getCategories()
    {
        return $this->getData(self::CATEGORIES_KEY);
    }

    /**
     * @param CategoryInterface[] $categories
     *
     * @return $this
     */
    public function setCategories(array $categories)
    {
        $this->setData(self::CATEGORIES_KEY, $categories);

        return $this;
    }
}

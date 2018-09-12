<?php

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\ProductsApiResponseInterface;

/**
 * Class ProductsApiResponse
 * @package Emartech\Emarsys\Model\Data
 */
class ProductsApiResponse extends ListApiResponseBase implements ProductsApiResponseInterface
{
    /**
     * @return \Emartech\Emarsys\Api\Data\ProductInterface[]
     */
    public function getProducts(): array
    {
        return $this->getData(self::PRODUCTS_KEY);
    }

    /**
     * @param array $products
     *
     * @return $this
     */
    public function setProducts(array $products): ProductsApiResponseInterface
    {
        $this->setData(self::PRODUCTS_KEY, $products);

        return $this;
    }
}

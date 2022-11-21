<?php

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\ProductInterface;
use Emartech\Emarsys\Api\Data\ProductsApiResponseInterface;

class ProductsApiResponse extends ListApiResponseBase implements ProductsApiResponseInterface
{
    /**
     * GetProducts
     *
     * @return ProductInterface[]
     */
    public function getProducts(): array
    {
        return $this->getData(self::PRODUCTS_KEY);
    }

    /**
     * SetProducts
     *
     * @param array $products
     *
     * @return ProductsApiResponseInterface
     */
    public function setProducts(array $products): ProductsApiResponseInterface
    {
        $this->setData(self::PRODUCTS_KEY, $products);

        return $this;
    }
}

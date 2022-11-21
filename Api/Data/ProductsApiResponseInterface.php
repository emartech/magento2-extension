<?php

namespace Emartech\Emarsys\Api\Data;

interface ProductsApiResponseInterface extends ListApiResponseBaseInterface
{
    public const PRODUCTS_KEY = 'products';

    /**
     * GetProducts
     *
     * @return \Emartech\Emarsys\Api\Data\ProductInterface[]
     */
    public function getProducts(): array;

    /**
     * SetProducts
     *
     * @param \Emartech\Emarsys\Api\Data\ProductInterface[] $products
     *
     * @return \Emartech\Emarsys\Api\Data\ProductsApiResponseInterface
     */
    public function setProducts(array $products): ProductsApiResponseInterface;
}

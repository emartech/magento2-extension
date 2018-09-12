<?php

namespace Emartech\Emarsys\Api\Data;

/**
 * Interface ProductsApiResponseInterface
 * @package Emartech\Emarsys\Api\Data
 */
interface ProductsApiResponseInterface extends ListApiResponseBaseInterface
{
    const PRODUCTS_KEY = 'products';

    /**
     * @return \Emartech\Emarsys\Api\Data\ProductInterface[]
     */
    public function getProducts(): array;

    /**
     * @param \Emartech\Emarsys\Api\Data\ProductInterface[] $products
     *
     * @return $this
     */
    public function setProducts(array $products): ProductsApiResponseInterface;
}

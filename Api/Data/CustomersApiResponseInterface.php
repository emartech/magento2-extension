<?php

namespace Emartech\Emarsys\Api\Data;

/**
 * Interface CustomersApiResponseInterface
 * @package Emartech\Emarsys\Api\Data
 */
interface CustomersApiResponseInterface extends ListApiResponseBaseInterface
{
    const CUSTOMERS_KEY = 'customers';

    /**
     * @return \Emartech\Emarsys\Api\Data\CustomerInterface[]
     */
    public function getCustomers(): array;

    /**
     * @param \Emartech\Emarsys\Api\Data\CustomerInterface[] $customers
     *
     * @return $this
     */
    public function setCustomers(array $customers): CustomersApiResponseInterface;
}

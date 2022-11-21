<?php

namespace Emartech\Emarsys\Api\Data;

interface CustomersApiResponseInterface extends ListApiResponseBaseInterface
{
    public const CUSTOMERS_KEY = 'customers';

    /**
     * GetCustomers
     *
     * @return \Emartech\Emarsys\Api\Data\CustomerInterface[]
     */
    public function getCustomers(): array;

    /**
     * SetCustomers
     *
     * @param \Emartech\Emarsys\Api\Data\CustomerInterface[] $customers
     *
     * @return \Emartech\Emarsys\Api\Data\CustomersApiResponseInterface
     */
    public function setCustomers(array $customers): CustomersApiResponseInterface;
}

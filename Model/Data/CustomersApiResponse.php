<?php

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\CustomerInterface;
use Emartech\Emarsys\Api\Data\CustomersApiResponseInterface;

class CustomersApiResponse extends ListApiResponseBase implements CustomersApiResponseInterface
{
    /**
     * GetCustomers
     *
     * @return CustomerInterface[]
     */
    public function getCustomers(): array
    {
        return $this->getData(self::CUSTOMERS_KEY);
    }

    /**
     * SetCustomers
     *
     * @param CustomerInterface[] $customers
     *
     * @return CustomersApiResponseInterface
     */
    public function setCustomers(array $customers): CustomersApiResponseInterface
    {
        $this->setData(self::CUSTOMERS_KEY, $customers);

        return $this;
    }
}

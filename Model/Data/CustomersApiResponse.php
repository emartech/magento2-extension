<?php

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\CustomersApiResponseInterface;

/**
 * Class CustomersApiResponse
 * @package Emartech\Emarsys\Model\Data
 */
class CustomersApiResponse extends ListApiResponseBase implements CustomersApiResponseInterface
{
    /**
     * @return \Emartech\Emarsys\Api\Data\CustomerInterface[]
     */
    public function getCustomers(): array
    {
        return $this->getData(self::CUSTOMERS_KEY);
    }

    /**
     * @param \Emartech\Emarsys\Api\Data\CustomerInterface[] $customers
     *
     * @return $this
     */
    public function setCustomers(array $customers): CustomersApiResponseInterface
    {
        $this->setData(self::CUSTOMERS_KEY, $customers);
        return $this;
    }
}

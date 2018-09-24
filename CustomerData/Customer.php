<?php

namespace Emartech\Emarsys\CustomerData;

use Magento\Customer\CustomerData\Customer as OriginalCustomerData;

/**
 * Class Customer
 * @package Emartech\Emarsys\CustomerData
 */
class Customer extends OriginalCustomerData
{
    /**
     * @param OriginalCustomerData $subject
     * @param                      $result
     *
     * @return array
     */
    public function afterGetSectionData(\Magento\Customer\CustomerData\Customer $subject, $result)
    {
        $customerId = $subject->currentCustomer->getCustomerId();
        if ($customerId) {
            $customer = $subject->currentCustomer->getCustomer();
            $result['id'] = $customerId;
            $result['email'] = $customer->getEmail();
        }

        return $result;
    }
}

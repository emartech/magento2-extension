<?php

namespace Emartech\Emarsys\CustomerData;

use Magento\Customer\CustomerData\Customer as OriginalCustomerData;

class Customer extends OriginalCustomerData
{
    /**
     * AfterGetSectionData
     *
     * @param OriginalCustomerData $subject
     * @param array                $result
     *
     * @return array
     */
    public function afterGetSectionData(OriginalCustomerData $subject, array $result): array
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

<?php

namespace Emartech\Emarsys\CustomerData;

use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Customer\CustomerData\Customer as OriginalCustomerData;

/**
 * Class Customer
 * @package Emartech\Emarsys\CustomerData
 */
class Customer extends OriginalCustomerData
{
    /**
     * @var CurrentCustomer
     */
    private $currentCustomer;

    /**
     * Customer constructor.
     *
     * @param CurrentCustomer $currentCustomer
     */
    public function __construct(
        CurrentCustomer $currentCustomer
    ) {
        $this->currentCustomer = $currentCustomer;
    }

    /**
     * @param OriginalCustomerData $subject
     * @param                      $result
     *
     * @return array
     */
    public function afterGetSectionData(\Magento\Customer\CustomerData\Customer $subject, $result)
    {
        $customerId = $this->currentCustomer->getCustomerId();
        if ($customerId) {
            $customer = $this->currentCustomer->getCustomer();
            $result['id'] = $customerId;
            $result['email'] = $customer->getEmail();
        }

        return $result;
    }
}
<?php

namespace Emartech\Emarsys\CustomerData;

use Magento\Customer\Helper\Session\CurrentCustomer;

class Customer extends \Magento\Customer\CustomerData\Customer
{
    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;

    public function __construct(
        CurrentCustomer $currentCustomer
    ) {
        $this->currentCustomer = $currentCustomer;
    }

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
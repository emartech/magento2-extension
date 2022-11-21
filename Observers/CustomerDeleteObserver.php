<?php


namespace Emartech\Emarsys\Observers;

use Emartech\Emarsys\Helper\CustomerEventHandler;
use Exception;
use Magento\Customer\Model\Customer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class CustomerDeleteObserver implements ObserverInterface
{
    /**
     * @var CustomerEventHandler
     */
    private $customerEventHandler;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param CustomerEventHandler $customerEventHandler
     * @param LoggerInterface      $logger
     */
    public function __construct(
        CustomerEventHandler $customerEventHandler,
        LoggerInterface $logger
    ) {
        $this->customerEventHandler = $customerEventHandler;
        $this->logger = $logger;
    }

    /**
     * Execute
     *
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var Customer $customer */
        $customer = $observer->getEvent()->getCustomer();

        try {
            $this->customerEventHandler->storeUserDataDirectly(
                [
                    'customer_id' => $customer->getId(),
                    'email'       => $customer->getEmail(),
                ],
                $customer->getId(),
                $customer->getWebsiteId(),
                $customer->getStoreId(),
                'customers/delete'
            );
        } catch (Exception $e) {
            $this->logger->warning('Emartech\\Emarsys\\Observers\\CustomerDeleteObserver: ' . $e->getMessage());
        }
    }
}

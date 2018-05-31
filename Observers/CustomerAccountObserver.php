<?php


namespace Emartech\Emarsys\Observers;

use Emartech\Emarsys\Helper\CustomerEventHandler;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class CustomerAccountObserver implements ObserverInterface
{
  /**
   * @var CustomerEventHandler
   */
  private $customerEventHandler;

  public function __construct(CustomerEventHandler $customerEventHandler)
  {
    $this->customerEventHandler = $customerEventHandler;
  }

  /**
   * @param Observer $observer
   * @return void
   * @throws \Exception
   */
  public function execute(Observer $observer)
  {
    $customerId = $observer->getEvent()->getCustomer()->getId();

    try {
      $this->customerEventHandler->store('customer_account', $customerId);
    } catch (\Exception $e) {
      $this->logger->warning('Emartech\\Emarsys\\Observers\\CustomerAccountObserver: ' . $e->getMessage());
    }
  }
}
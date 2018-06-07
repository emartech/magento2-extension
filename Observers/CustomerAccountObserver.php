<?php


namespace Emartech\Emarsys\Observers;

use Emartech\Emarsys\Helper\CustomerEventHandler;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class CustomerAccountObserver implements ObserverInterface
{
  /**
   * @var CustomerEventHandler
   */
  private $customerEventHandler;
  /**
   * @var LoggerInterface
   */
  private $logger;

  public function __construct(CustomerEventHandler $customerEventHandler, LoggerInterface $logger)
  {
    $this->customerEventHandler = $customerEventHandler;
    $this->logger = $logger;
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
      $this->customerEventHandler->store('customers/update', $customerId);
    } catch (\Exception $e) {
      $this->logger->warning('Emartech\\Emarsys\\Observers\\CustomerAccountObserver: ' . $e->getMessage());
    }
  }
}
<?php


namespace Emartech\Emarsys\Observers;

use Emartech\Emarsys\Helper\CustomerEventHandler;
use Magento\Customer\Model\Customer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;
use Psr\Log\LoggerInterface;

class CustomerBeforeObserver implements ObserverInterface
{
  /**
   * @var CustomerEventHandler
   */
  private $customerEventHandler;
  /** @var Registry  */
  protected $registry;
  /**
   * @var LoggerInterface
   */
  private $logger;

  public function __construct(
    CustomerEventHandler $customerEventHandler,
    Registry $registry,
    LoggerInterface $logger
  ) {
    $this->customerEventHandler = $customerEventHandler;
    $this->registry = $registry;
    $this->logger = $logger;
  }

  /**
   * @param Observer $observer
   * @return CustomerBeforeObserver
   */
  public function execute(Observer $observer)
  {
    /** @var Customer $customer */
    $customer = $observer->getEvent()->getCustomer();
    if ($customer->hasDataChanges()) {
      $this->registry->unregister('emarsys_data_changes');
      $this->registry->register('emarsys_data_changes', true);
    }
    if ($customer->isObjectNew()) {
      $this->registry->unregister('emarsys_new_customer');
      $this->registry->register('emarsys_new_customer', true);
    }

    return $this;
  }
}
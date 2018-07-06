<?php


namespace Emartech\Emarsys\Observers;

use Emartech\Emarsys\Helper\CustomerEventHandler;
use Emartech\Emarsys\Helper\SubscriptionEventHandler;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Newsletter\Model\Subscriber;
use Psr\Log\LoggerInterface;

class SubscriptionObserver implements ObserverInterface
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
   * @var SubscriptionEventHandler
   */
  private $subscriptionEventHandler;

  public function __construct(
    CustomerEventHandler $customerEventHandler,
    SubscriptionEventHandler $subscriptionEventHandler,
    LoggerInterface $logger
  )
  {
    $this->customerEventHandler = $customerEventHandler;
    $this->logger = $logger;
    $this->subscriptionEventHandler = $subscriptionEventHandler;
  }

  /**
   * @param Observer $observer
   * @return void
   * @throws \Exception
   */
  public function execute(Observer $observer)
  {
    /** @var Subscriber $subscriber */
    $subscriber = $observer->getSubscriber();

    try {
      if ($subscriber->getCustomerId()) {
        $this->customerEventHandler->store('customers/update', $subscriber->getCustomerId());
      } else {
        $this->subscriptionEventHandler->store($subscriber);
      }
    } catch (\Exception $e) {
      $this->logger->warning('Emartech\\Emarsys\\Observers\\CustomerAccountObserver: ' . $e->getMessage());
    }
  }
}
<?php


namespace Emartech\Emarsys\Observers;

use Emartech\Emarsys\Helper\SalesEventHandler;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

class SalesOrderObserver implements ObserverInterface
{
  /**
   * @var SalesEventHandler
   */
  private $salesEventHandler;
  /**
   * @var LoggerInterface
   */
  private $logger;

  public function __construct(SalesEventHandler $salesEventHandler, LoggerInterface $logger)
  {
    $this->salesEventHandler = $salesEventHandler;
    $this->logger = $logger;
  }

  /**
   * @param Observer $observer
   * @return void
   * @throws \Exception
   */
  public function execute(Observer $observer)
  {
    /** @var Order $order */
    $order = $observer->getEvent()->getOrder();

    try {
      $this->salesEventHandler->store($order);
    } catch (\Exception $e) {
      $this->logger->warning('Emartech\\Emarsys\\Observers\\SalesOrderObserver: ' . $e->getMessage());
    }
  }
}
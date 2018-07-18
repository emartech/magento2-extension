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

    $orderData = $order->getData();
    $this->logger->info(json_encode($orderData));
    $orderItems = $order->getAllItems();
    $orderData['items'] = [];
    $orderData['addresses'] = [];

    foreach ($orderItems as $item) {
      $arrayItem = $item->toArray();
      $parentItem = $item->getParentItem();
      if (!is_null($parentItem)) {
        $arrayItem['parent_item'] = $parentItem->toArray();
      }
      $orderData['items'][] = $arrayItem;
    }

    $orderData['addresses']['shipping'] = $order->getShippingAddress()->toArray();
    $orderData['addresses']['billing'] = $order->getBillingAddress()->toArray();

    $orderData['payments'] = $order->getAllPayments();

    $orderData['shipments'] = $order->getShipmentsCollection()->toArray();
    $orderData['tracks'] = $order->getTracksCollection()->toArray();

    $event_type = $this->getEventType($orderData['state']);

    try {
      $this->salesEventHandler->store($event_type, $orderData);
    } catch (\Exception $e) {
      $this->logger->warning('Emartech\\Emarsys\\Observers\\OrderObserver: ' . $e->getMessage());
    }
  }

  /**
   * @param $state
   * @return string
   */
  private function getEventType($state)
  {
    if ($state === 'new') {
      return 'orders/create';
    }

    if ($state === 'canceled') {
      return 'orders/cancelled';
    }

    if ($state === 'complete') {
      return 'orders/fulfilled';
    }

    return 'orders/' . $state;
  }
}
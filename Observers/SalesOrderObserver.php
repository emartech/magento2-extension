<?php

namespace Emartech\Emarsys\Observers;

use Emartech\Emarsys\Helper\SalesEventHandler;
use Exception;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\Data\OrderInterface;
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

    /**
     * @param SalesEventHandler $salesEventHandler
     * @param LoggerInterface   $logger
     */
    public function __construct(
        SalesEventHandler $salesEventHandler,
        LoggerInterface $logger
    ) {

        $this->salesEventHandler = $salesEventHandler;
        $this->logger = $logger;
    }

    /**
     * Execute
     *
     * @param Observer $observer
     *
     * @return void
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        /** @var Order $order */
        /** @noinspection PhpUndefinedMethodInspection */
        $order = $observer->getEvent()->getOrder();

        if ($order->getState() != $order->getOrigData(OrderInterface::STATE)
            && $order->getState() == Order::STATE_COMPLETE
        ) {
            try {
                $this->salesEventHandler->store($order);
            } catch (Exception $e) {
                $this->logger->warning('Emartech\\Emarsys\\Observers\\SalesOrderObserver: ' . $e->getMessage());
            }
        }
    }
}

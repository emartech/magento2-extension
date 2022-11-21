<?php

namespace Emartech\Emarsys\Observers;

use Emartech\Emarsys\Helper\RefundEventHandler;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order\Creditmemo;
use Psr\Log\LoggerInterface;

class CreditmemoObserver implements ObserverInterface
{
    /**
     * @var RefundEventHandler
     */
    private $refundEventHandler;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param RefundEventHandler $refundEventHandler
     * @param LoggerInterface    $logger
     */
    public function __construct(
        RefundEventHandler $refundEventHandler,
        LoggerInterface $logger
    ) {
        $this->refundEventHandler = $refundEventHandler;
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
        /** @var Creditmemo $order */
        /** @noinspection PhpUndefinedMethodInspection */
        $order = $observer->getEvent()->getCreditmemo();

        try {
            $this->refundEventHandler->store($order);
        } catch (\Exception $e) {
            $this->logger->warning(__CLASS__ . ': ' . $e->getMessage());
        }
    }
}

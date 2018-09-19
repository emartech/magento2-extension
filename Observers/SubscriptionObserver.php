<?php

namespace Emartech\Emarsys\Observers;

use Emartech\Emarsys\Helper\CustomerEventHandler;
use Emartech\Emarsys\Helper\SubscriptionEventHandler;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Newsletter\Model\Subscriber;
use Psr\Log\LoggerInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class SubscriptionObserver
 * @package Emartech\Emarsys\Observers
 */
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

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * SubscriptionObserver constructor.
     *
     * @param CustomerEventHandler     $customerEventHandler
     * @param SubscriptionEventHandler $subscriptionEventHandler
     * @param LoggerInterface          $logger
     * @param StoreManagerInterface    $storeManager
     */
    public function __construct(
        CustomerEventHandler $customerEventHandler,
        SubscriptionEventHandler $subscriptionEventHandler,
        LoggerInterface $logger,
        StoreManagerInterface $storeManager
    ) {
        $this->customerEventHandler = $customerEventHandler;
        $this->logger = $logger;
        $this->subscriptionEventHandler = $subscriptionEventHandler;
        $this->storeManager = $storeManager;
    }

    /**
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var Subscriber $subscriber */
        $subscriber = $observer->getSubscriber();

        try {
            $store = $this->storeManager->getStore($subscriber->getStoreId());

            if ($subscriber->getCustomerId()) {
                $this->customerEventHandler->store(
                    $subscriber->getCustomerId(),
                    $store->getWebsiteId(),
                    $store->getId()
                );
            } else {
                $this->subscriptionEventHandler->store(
                    $subscriber,
                    $store->getWebsiteId(),
                    $store->getId(),
                    $this->subscriptionEventHandler->getEventType($observer->getEvent()->getName())
                );
            }
        } catch (\Exception $e) {
            $this->logger->warning('Emartech\\Emarsys\\Observers\\SubscriptionObserver: ' . $e->getMessage());
        }
    }
}
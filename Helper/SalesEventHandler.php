<?php

namespace Emartech\Emarsys\Helper;

use Psr\Log\LoggerInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Sales\Model\Order;
use Magento\Sales\Api\Data\OrderItemInterface;

use Emartech\Emarsys\Model\EventFactory;
use Emartech\Emarsys\Model\ResourceModel\Event\CollectionFactory as EventCollectionFactory;
use Emartech\Emarsys\Api\EventRepositoryInterface;

/**
 * Class SalesEventHandler
 * @package Emartech\Emarsys\Helper
 */
class SalesEventHandler extends BaseEventHandler
{

    /**
     * SalesEventHandler constructor.
     *
     * @param ConfigReader             $configReader
     * @param EventFactory             $eventFactory
     * @param EventRepositoryInterface $eventRepository
     * @param EventCollectionFactory   $eventCollectionFactory
     * @param Context                  $context
     * @param LoggerInterface          $logger
     * @param StoreManagerInterface    $storeManager
     * @param JsonSerializer           $jsonSerializer
     */
    public function __construct(
        ConfigReader $configReader,
        EventFactory $eventFactory,
        EventRepositoryInterface $eventRepository,
        EventCollectionFactory $eventCollectionFactory,
        Context $context,
        LoggerInterface $logger,
        StoreManagerInterface $storeManager,
        JsonSerializer $jsonSerializer
    ) {
        parent::__construct(
            $logger,
            $storeManager,
            $configReader,
            $eventFactory,
            $eventRepository,
            $eventCollectionFactory,
            $jsonSerializer,
            $context
        );
    }

    /**
     * @param Order $order
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function store(Order $order)
    {
        $storeId = $order->getStoreId();

        if (!$this->isEnabledForStore($storeId)) {
            return false;
        }

        $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();

        $orderData = $order->getData();
        $orderItems = $order->getAllItems();
        $orderData['items'] = [];
        $orderData['addresses'] = [];

        /** @var \Magento\Sales\Model\Order\Item $item */
        foreach ($orderItems as $item) {
            $arrayItem = $item->toArray();
            $parentItem = $item->getParentItem();
            if ($parentItem instanceof OrderItemInterface) {
                $arrayItem['parent_item'] = $parentItem->toArray();
            }
            $orderData['items'][] = $arrayItem;
        }

        if (array_key_exists('shipping', $orderData['addresses'])) {
            $orderData['addresses']['shipping'] = $order->getShippingAddress()->toArray();
        }

        $orderData['addresses']['billing'] = $order->getBillingAddress()->toArray();
        $orderData['payments'] = $order->getAllPayments();
        $orderData['shipments'] = $order->getShipmentsCollection()->toArray();
        $orderData['tracks'] = $order->getTracksCollection()->toArray();

        $this->saveEvent(
            $websiteId,
            $storeId,
            $this->getOrderEventType($order->getState()),
            $order->getId(),
            $orderData
        );

        return true;
    }

    /**
     * @param string $state
     *
     * @return string
     */
    private function getOrderEventType($state)
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

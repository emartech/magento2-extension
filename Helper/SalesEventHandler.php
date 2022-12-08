<?php

namespace Emartech\Emarsys\Helper;

use Emartech\Emarsys\Api\EventRepositoryInterface;
use Emartech\Emarsys\Helper\Json as JsonSerializer;
use Emartech\Emarsys\Model\EventFactory;
use Emartech\Emarsys\Model\ResourceModel\Event\CollectionFactory as EventCollectionFactory;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Model\Order;
use Magento\Store\Model\StoreManagerInterface;

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
     * @param StoreManagerInterface    $storeManager
     * @param JsonSerializer           $jsonSerializer
     */
    public function __construct(
        ConfigReader $configReader,
        EventFactory $eventFactory,
        EventRepositoryInterface $eventRepository,
        EventCollectionFactory $eventCollectionFactory,
        Context $context,
        StoreManagerInterface $storeManager,
        JsonSerializer $jsonSerializer
    ) {
        parent::__construct(
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
     * Store
     *
     * @param Order $order
     *
     * @return bool
     * @throws AlreadyExistsException
     * @throws NoSuchEntityException
     */
    public function store(Order $order): bool
    {
        $storeId = $order->getStoreId();
        if (!$storeId) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        if (!$this->isEnabledForStore($storeId)) {
            return false;
        }

        $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();

        $orderData = $order->getData();
        $orderData['id'] = $order->getId();
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

        if ($order->getShippingAddress()) {
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
     * GetOrderEventType
     *
     * @param string $state
     *
     * @return string
     */
    private function getOrderEventType(string $state): string
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

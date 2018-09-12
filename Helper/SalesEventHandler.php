<?php


namespace Emartech\Emarsys\Helper;


use Emartech\Emarsys\Api\Data\ConfigInterface;
use Emartech\Emarsys\Model\ResourceModel\Event;
use Emartech\Emarsys\Model\SettingsFactory;
use Emartech\Emarsys\Model\EventFactory;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Newsletter\Model\Subscriber;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class SalesEventHandler extends AbstractHelper
{
    protected $logger;
    protected $orderFactory;
    protected $eventFactory;
    protected $eventResource;
    protected $subscriber;
    /** @var ConfigReader */
    protected $configReader;
    /** @var StoreManagerInterface */
    protected $storeManager;

    public function __construct(
        ConfigReader $configReader,
        OrderFactory $orderFactory,
        EventFactory $eventFactory,
        StoreManagerInterface $storeManager,
        Event $eventResource,
        Subscriber $subscriber,
        LoggerInterface $logger
    )
    {
        $this->orderFactory = $orderFactory;
        $this->eventFactory = $eventFactory;
        $this->eventResource = $eventResource;
        $this->logger = $logger;
        $this->subscriber = $subscriber;

        $this->configReader = $configReader;
        $this->storeManager = $storeManager;
    }

    /**
     * @param Order $order
     * @throws \Exception
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function store(Order $order)
    {
        $storeId = $order->getStoreId();
        $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();
        
        if (!$this->configReader->isEnabledForStore(ConfigInterface::SALES_EVENTS, $storeId)) return;

        $orderData = $order->getData();
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

        if (array_key_exists('shipping', $orderData['addresses'])) {
            $orderData['addresses']['shipping'] = $order->getShippingAddress()->toArray();
        }
        $orderData['addresses']['billing'] = $order->getBillingAddress()->toArray();

        $orderData['payments'] = $order->getAllPayments();

        $orderData['shipments'] = $order->getShipmentsCollection()->toArray();
        $orderData['tracks'] = $order->getTracksCollection()->toArray();

        /** @var \Emartech\Emarsys\Model\Event $eventModel */
        $eventModel = $this->eventFactory->create();
        $eventModel->setWebsiteId($websiteId);
        $eventModel->setStoreId($storeId);
        $eventModel->setData('event_type', $this->getEventType($orderData['state']));
        $eventModel->setData('event_data', json_encode($orderData));
        $this->eventResource->save($eventModel);
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
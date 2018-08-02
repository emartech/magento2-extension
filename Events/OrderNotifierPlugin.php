<?php

namespace Emartech\Emarsys\Events;

use Magento\Sales\Model\OrderNotifier;
use Magento\Sales\Model\Order;
use Emartech\Emarsys\Helper\Data as EmarsysData;
use Emartech\Emarsys\Model\EventFactory as EmarsysEventFactory;
use Emartech\Emarsys\Model\EventRepository;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Helper\View as CustomerViewHelper;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;

/**
 * Order Events
 */
class OrderNotifierPlugin
{
    const EVENT_ORDER_SEND = 'order_';

    /**
     * @var EmarsysData
     */
    public $emarsysData;
    /**
     * @var EmarsysEventFactory
     */
    public $eventFactory;
    /**
     * @var EventRepository
     */
    public $eventRepository;
    /**
     * @var CustomerRepositoryInterface
     */
    public $customerRepositoryInterface;
    /**
     * @var CustomerViewHelper
     */
    public $customerViewHelper;
    /**
     * @var Json
     */
    public $json;

    /**
     * NotifySenderPlugin constructor.
     * @param EmarsysData $emarsysData
     * @param EmarsysEventFactory $eventFactory
     * @param EventRepository $eventRepository
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param CustomerViewHelper $customerViewHelper
     * @param Json $json
     */
    public function __construct(
        EmarsysData $emarsysData,
        EmarsysEventFactory $eventFactory,
        EventRepository $eventRepository,
        CustomerRepositoryInterface $customerRepositoryInterface,
        CustomerViewHelper $customerViewHelper,
        Json $json
    ) {
        $this->emarsysData = $emarsysData;
        $this->eventFactory = $eventFactory;
        $this->eventRepository = $eventRepository;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->customerViewHelper = $customerViewHelper;
        $this->json = $json;
    }

    /**
     * @param OrderNotifier $orderNotifier
     * @param callable $proceed
     * @param \Magento\Sales\Model\AbstractModel $model
     * @return mixed
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundNotify(
        OrderNotifier $orderNotifier,
        callable $proceed,
        \Magento\Sales\Model\AbstractModel $model
    ) {
        if (!$this->emarsysData->isEnabled(EmarsysData::MARKETING_EVENTS)) {
            return $proceed($model);
        }


        /** @var \Emartech\Emarsys\Model\Event $eventModel */
        $eventModel = $this->eventFactory->create();
        $eventModel->setEventType(self::EVENT_ORDER_SEND . $model->getState());

        $customer = [];
        if ($model->getCustomerId()) {
            $customer = $this->customerRepositoryInterface->getById($model->getCustomerId());
            $customer->setData('name', $this->customerViewHelper->getCustomerName($customer));
            $customer = $customer->__toArray();
        }

        $items = [];
        foreach ($model->getAllVisibleItems() as $item) {
            $items[] = $item->getData();
        }

        $data = [
            'order' => $model->getData(),
            'items' => $items,
            'customer' => $customer,
            'is_guest' => $model->getCustomerIsGuest(),
            'store' => $model->getStore()->getData(),
            'shipping_address' => $model->getShippingAddress()->getData(),
            'billing_address' => $model->getBillingAddress()->getData()
        ];

        $eventModel->setEventData($this->json->serialize($data));
        $this->eventRepository->save($eventModel);
    }

    /**
     * @param OrderSender $orderSender
     * @param callable $proceed
     * @param Order $order
     * @param bool $forceSyncMode
     * @return mixed
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function aroundSend(
        OrderSender $orderSender,
        callable $proceed,
        Order $order,
        $forceSyncMode = false
    ) {
        if (!$this->emarsysData->isEnabled(EmarsysData::MARKETING_EVENTS)) {
            return $proceed($order, $forceSyncMode);
        }

        /** @var \Emartech\Emarsys\Model\Event $eventModel */
        $eventModel = $this->eventFactory->create();
        $eventModel->setEventType(self::EVENT_ORDER_SEND . $order->getState());

        $customer = [];
        if ($order->getCustomerId()) {
            $customer = $this->customerRepositoryInterface->getById($order->getCustomerId());
            $customer->setData('name', $this->customerViewHelper->getCustomerName($customer));
            $customer = $customer->__toArray();
        }

        $items = [];
        foreach ($order->getAllVisibleItems() as $item) {
            $items[] = $item->getData();
        }

        $data = [
            'order' => $order->getData(),
            'items' => $items,
            'customer' => $customer,
            'is_guest' => $order->getCustomerIsGuest(),
            'store' => $order->getStore()->getData(),
            'shipping_address' => $order->getShippingAddress()->getData(),
            'billing_address' => $order->getBillingAddress()->getData()
        ];

        $eventModel->setEventData($this->json->serialize($data));
        $this->eventRepository->save($eventModel);
    }
}
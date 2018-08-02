<?php

namespace Emartech\Emarsys\Events;

use Magento\Shipping\Model\ShipmentNotifier;
use Magento\Sales\Model\Order;
use Emartech\Emarsys\Api\Data\ConfigInterface;
use Emartech\Emarsys\Helper\ConfigReader;
use Emartech\Emarsys\Model\EventFactory as EmarsysEventFactory;
use Emartech\Emarsys\Model\EventRepository;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Helper\View as CustomerViewHelper;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Order Events
 */
class ShipmentNotifierPlugin
{
    /**
     * @var ConfigReader
     */
    public $configReader;
    /**
     * @var EmarsysEventFactory
     */
    public $eventFactory;
    /**
     * @var EventRepository
     */
    public $eventRepository;

    /**
     * NotifySenderPlugin constructor.
     * @param ConfigReader $configReader
     * @param EmarsysEventFactory $eventFactory
     * @param EventRepository $eventRepository
     */
    public function __construct(
        ConfigReader $configReader,
        EmarsysEventFactory $eventFactory,
        EventRepository $eventRepository
    ) {
        $this->configReader = $configReader;
        $this->eventFactory = $eventFactory;
        $this->eventRepository = $eventRepository;
    }

    /**
     * @param ShipmentNotifier $shipmentNotifier
     * @param callable $proceed
     * @param \Magento\Sales\Model\AbstractModel $model
     * @return mixed
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function aroundNotify(
        ShipmentNotifier $shipmentNotifier,
        callable $proceed,
        \Magento\Sales\Model\AbstractModel $model
    ) {
        if ($this->configReader->isEnabled(ConfigInterface::MARKETING_EVENTS)) {
            return $proceed($model);
        }


        /** @var \Emartech\Emarsys\Model\Event $eventModel */
        $eventModel = $this->eventFactory->create();
        //$eventModel->setEventType(self::EVENT_CUSTOMER_NEW_ACCOUNT . $type);

        $data = [
            'order' => $model->getData(),
            'customer' => $model->getCustomer()->getData(),
            'is_guest' => ($model->getCustomer()->getId()) ? false : true,
            'store' => $model->getStore()->getData(),
            'shipping_address' => $model->getShippingAddress()->getData(),
            'billing_address' => $model->getBillingAddress()->getData()
        ];

        $eventModel->setEventData($this->json->serialize($data));
        $this->eventRepository->save($eventModel);
    }
}
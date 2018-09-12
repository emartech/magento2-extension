<?php

namespace Emartech\Emarsys\Events;

use Magento\Sales\Model\Order\Email\SenderBuilder;
use Emartech\Emarsys\Api\Data\ConfigInterface;
use Emartech\Emarsys\Helper\ConfigReader;
use Emartech\Emarsys\Model\EventFactory as EmarsysEventFactory;
use Emartech\Emarsys\Model\EventRepository;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Helper\View as CustomerViewHelper;
use Magento\Framework\Serialize\Serializer\Json;
use \Psr\Log\LoggerInterface;

/**
 * Order Events
 */
class SenderBuilderPlugin
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
     * @var LoggerInterface
     */
    public $logger;

    /**
     * NotifySenderPlugin constructor.
     * @param ConfigReader $configReader
     * @param EmarsysEventFactory $eventFactory
     * @param EventRepository $eventRepository
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param CustomerViewHelper $customerViewHelper
     * @param Json $json
     * @param LoggerInterface $logger
     */
    public function __construct(
        ConfigReader $configReader,
        EmarsysEventFactory $eventFactory,
        EventRepository $eventRepository,
        CustomerRepositoryInterface $customerRepositoryInterface,
        CustomerViewHelper $customerViewHelper,
        Json $json,
        LoggerInterface $logger
    ) {
        $this->configReader = $configReader;
        $this->eventFactory = $eventFactory;
        $this->eventRepository = $eventRepository;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->customerViewHelper = $customerViewHelper;
        $this->json = $json;
        $this->logger = $logger;
    }

    /**
     * @param SenderBuilder $senderBuilder
     * @param callable $proceed
     * @return mixed
     */
    public function aroundSend(
        SenderBuilder $senderBuilder,
        callable $proceed
    ) {
        //----
        //sales_email/general/async_sending - should be disabled
        //----
        try {
            $reflection = new \ReflectionClass($senderBuilder);

            /** @var \Magento\Sales\Model\Order\Email\Container\OrderIdentity $identityContainer */
            $identityContainer = $reflection->getProperty('identityContainer');
            $identityContainer->setAccessible(true);
            $identityContainer = $identityContainer->getValue($senderBuilder);

            if (!$this->configReader->isEnabledForWebsite(ConfigInterface::MARKETING_EVENTS, $identityContainer->getStore()->getWebsiteId())) {
                return $proceed();
            }

            /** @var \Magento\Sales\Model\Order\Email\Container\Template $templateContainer */
            $templateContainer = $reflection->getProperty('templateContainer');
            $templateContainer->setAccessible(true);
            $templateContainer = $templateContainer->getValue($senderBuilder);


            /** @var \Emartech\Emarsys\Model\Event $eventModel */
            $eventModel = $this->eventFactory->create();

            $eventModel->setEventType($templateContainer->getTemplateId());

            $data = [
                'customerName' => $identityContainer->getCustomerName(),
                'customerEmail' => $identityContainer->getCustomerEmail(),
                'emailCopyTo' => $identityContainer->getEmailCopyTo(),
            ];

            foreach ($templateContainer->getTemplateVars() as $key => $value) {
                if ($key == 'order') {
                    /** @var \Magento\Sales\Model\Order $order */
                    $order = $value;
                    $value = $value->getData();
                    $items = [];
                    foreach ($order->getAllVisibleItems() as $item) {
                        $items[] = $item->getData();
                    }
                    $value['items'] = $items;
                    $value['addresses'] = [
                        'shipping' => $order->getShippingAddress()->getData(),
                        'billing' => $order->getBillingAddress()->getData()
                    ];
                    $data['is_guest'] = $order->getCustomerIsGuest();
                    $customer = [];
                    if ($order->getCustomerId()) {
                        /** @var \Magento\Customer\Model\Data\Customer $customer */
                        $customer = $this->customerRepositoryInterface->getById($order->getCustomerId());
                        $customer->setData('name', $this->customerViewHelper->getCustomerName($customer));
                        $customer = $customer->__toArray();
                    }
                    $data['customer'] = $customer;
                    $value['payment'] = $order->getPayment()->getData();

                    $shipmentsCollection = $order->getShipmentsCollection();
                    $shipments = [];
                    foreach ($shipmentsCollection->getItems() as $shipment) {
                        $tracksCollection = $shipment->getTracksCollection();
                        $tracks = [];
                        foreach ($tracksCollection->getItems() as $track) {
                            $tracks[] = $track->getData();
                        }
                        $shipmentData = $shipment->getData();
                        $shipmentData['tracks'] = $tracks;
                        $shipments[] = $shipmentData;
                    }
                    $value['shipments'] = $shipments;
                }

                if ($key == 'invoice') {
                    /** @var \Magento\Sales\Model\Order\Invoice $invoice */
                    $invoice = $value;
                    $value = $value->getData();
                    $items = [];
                    foreach ($invoice->getAllItems() as $item) {
                        $items[] = $item->getData();
                    }
                    $value['items'] = $items;

                    $comments = [];
                    foreach ($invoice->getComments() as $comment) {
                        $comments[] = $comment->getData();
                    }
                    $value['comments'] = $comments;
                }

                if ($key == 'creditmemo') {
                    /** @var \Magento\Sales\Model\Order\Creditmemo $creditmemo */
                    $creditmemo = $value;
                    $value = $value->getData();
                    $items = [];
                    foreach ($creditmemo->getAllItems() as $item) {
                        $items[] = $item->getData();
                    }
                    $value['items'] = $items;

                    $comments = [];
                    foreach ($creditmemo->getComments() as $comment) {
                        $comments[] = $comment->getData();
                    }
                    $value['comments'] = $comments;
                }

                $data[$key] = is_object($value) ? $value->getData() : $value;
            }

            $eventModel->setEventData($this->json->serialize($data));
            $this->eventRepository->save($eventModel);
        } catch (\Exception $e) {
            $this->logger->warning('Emartech\\Emarsys\\Events\\SenderBuilderPlugin: ' . $e->getMessage());
        }
    }
}
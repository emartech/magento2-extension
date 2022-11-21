<?php

namespace Emartech\Emarsys\Events;

use Emartech\Emarsys\Api\Data\ConfigInterface;
use Emartech\Emarsys\Helper\ConfigReader;
use Emartech\Emarsys\Helper\Customer as CustomerHelper;
use Emartech\Emarsys\Helper\Json;
use Emartech\Emarsys\Model\EventFactory as EmarsysEventFactory;
use Emartech\Emarsys\Model\EventRepository;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Sales\Model\AbstractModel;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Container\OrderIdentity;
use Magento\Sales\Model\Order\Email\Container\Template as TemplateContainer;
use Magento\Sales\Model\Order\Email\SenderBuilder;
use Psr\Log\LoggerInterface;
use ReflectionClass;

class SenderBuilderPlugin
{
    /**
     * @var ConfigReader
     */
    private $configReader;
    /**
     * @var EmarsysEventFactory
     */
    private $eventFactory;
    /**
     * @var EventRepository
     */
    private $eventRepository;
    /**
     * @var Json
     */
    private $json;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var int
     */
    private $websiteId;
    /**
     * @var CustomerHelper
     */
    private $customerHelper;

    /**
     * SenderBuilderPlugin constructor.
     *
     * @param ConfigReader        $configReader
     * @param EmarsysEventFactory $eventFactory
     * @param EventRepository     $eventRepository
     * @param Json                $json
     * @param CustomerHelper      $customerHelper
     * @param LoggerInterface     $logger
     */
    public function __construct(
        ConfigReader $configReader,
        EmarsysEventFactory $eventFactory,
        EventRepository $eventRepository,
        Json $json,
        CustomerHelper $customerHelper,
        LoggerInterface $logger
    ) {
        $this->configReader = $configReader;
        $this->eventFactory = $eventFactory;
        $this->eventRepository = $eventRepository;
        $this->json = $json;
        $this->logger = $logger;
        $this->customerHelper = $customerHelper;
    }

    /**
     * AroundSend
     *
     * @param SenderBuilder $senderBuilder
     * @param callable      $proceed
     *
     * @return mixed
     */
    public function aroundSend(SenderBuilder $senderBuilder, callable $proceed)
    {
        //----
        //sales_email/general/async_sending - should be disabled
        //----
        try {
            $reflection = new ReflectionClass(SenderBuilder::class);
            /** @var OrderIdentity $identityContainer */
            $identityContainer = $reflection->getProperty('identityContainer');
            $identityContainer->setAccessible(true);
            $identityContainer = $identityContainer->getValue($senderBuilder);
            $storeId = $identityContainer->getStore()->getStoreId();
            $this->websiteId = $identityContainer->getStore()->getWebsiteId();

            if (!$this->configReader->isEnabledForStore(
                ConfigInterface::MARKETING_EVENTS,
                $storeId
            )) {
                return $proceed();
            }

            $sendMagentoEmail = $this->configReader->isEnabledForStore(
                ConfigInterface::MAGENTO_SEND_EMAIL,
                $storeId
            );

            /** @var TemplateContainer $templateContainer */
            $templateContainer = $reflection->getProperty('templateContainer');
            $templateContainer->setAccessible(true);
            $templateContainer = $templateContainer->getValue($senderBuilder);

            $data = $this->parseTemplateVars($templateContainer);
            $data['customerName'] = $identityContainer->getCustomerName();
            $data['customerEmail'] = $identityContainer->getCustomerEmail();
            $data['emailCopyTo'] = $identityContainer->getEmailCopyTo();

            $this->saveEvent(
                $this->websiteId,
                $storeId,
                $templateContainer->getTemplateId(),
                $data['order']['entity_id'],
                $data
            );
        } catch (\Exception $e) {
            $this->logger->warning('Emartech\\Emarsys\\Events\\SenderBuilderPlugin: ' . $e->getMessage());
            $sendMagentoEmail = true;
        }

        if ($sendMagentoEmail) {
            return $proceed();
        }
    }

    /**
     * ParseTemplateVars
     *
     * @param TemplateContainer $templateContainer
     *
     * @return array
     */
    private function parseTemplateVars(TemplateContainer $templateContainer): array
    {
        $returnArray = [];

        foreach ($templateContainer->getTemplateVars() as $key => $value) {
            switch ($key) {
                case 'order':
                    $this->parseOrderVars($key, $value, $returnArray);
                    break;
                case 'invoice':
                case 'creditmemo':
                    $this->parseVars($key, $value, $returnArray);
                    break;
            }
        }

        return $returnArray;
    }

    /**
     * ParseOrderVars
     *
     * @param string $key
     * @param Order  $order
     * @param array  $data
     *
     * @return void
     */
    private function parseOrderVars(string $key, Order $order, array &$data): void
    {
        $data[$key] = $order->getData();
        $items = [];
        foreach ($order->getAllItems() as $item) {
            $items[] = $item->getData();
        }
        $data[$key]['items'] = $items;

        $data[$key]['addresses'] = [];
        if ($order->getShippingAddress()) {
            $data[$key]['addresses']['shipping'] = $order->getShippingAddress()->toArray();
        }
        $data[$key]['addresses']['billing'] = $order->getBillingAddress()->toArray();
        $data['is_guest'] = $order->getCustomerIsGuest();
        if ($order->getCustomerId()) {
            $data['customer'] = false;
            $customerData = $this->customerHelper->getOneCustomer(
                $order->getCustomerId(),
                $this->websiteId,
                true
            );
            if (false !== $customerData) {
                $data['customer'] = $customerData;
            }
        }
        $data[$key]['payment'] = $order->getPayment()->getData();

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
        $data[$key]['shipments'] = $shipments;
        foreach ($order->getStatusHistoryCollection() as $historyItem) {
            $comment = [];
            $comment['comment'] = $historyItem->getComment();
            $comment['status'] = $historyItem->getStatus();
            $data[$key]['comments'][] = $comment;
        }
    }

    /**
     * ParseVars
     *
     * @param string        $key
     * @param AbstractModel $object
     * @param array         $data
     *
     * @return void
     */
    private function parseVars(string $key, AbstractModel $object, array &$data): void
    {
        $data[$key] = $object->getData();
        $items = [];
        /** @var AbstractModel $item */
        foreach ($object->getAllItems() as $item) {
            $items[] = $item->getData();
        }
        $data[$key]['items'] = $items;

        $comments = [];
        /** @var AbstractModel $comment */
        foreach ($object->getComments() as $comment) {
            $comments[] = $comment->getData();
        }
        $data[$key]['comments'] = $comments;
    }

    /**
     * SaveEvent
     *
     * @param int    $websiteId
     * @param int    $storeId
     * @param string $type
     * @param int    $entityId
     * @param array  $data
     *
     * @return void
     * @throws AlreadyExistsException
     */
    private function saveEvent(int $websiteId, int $storeId, string $type, int $entityId, array $data): void
    {
        $data = $this->json->serialize($data);

        $eventModel = $this->eventFactory
            ->create()
            ->setEntityId($entityId)
            ->setWebsiteId($websiteId)
            ->setStoreId($storeId)
            ->setEventType($type)
            ->setEventData($data);

        $this->eventRepository->save($eventModel);
    }
}

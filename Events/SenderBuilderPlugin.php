<?php

namespace Emartech\Emarsys\Events;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\SenderBuilder;
use Emartech\Emarsys\Api\Data\ConfigInterface;
use Emartech\Emarsys\Helper\ConfigReader;
use Emartech\Emarsys\Model\EventFactory as EmarsysEventFactory;
use Emartech\Emarsys\Model\EventRepository;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Helper\View as CustomerViewHelper;
use Magento\Framework\Serialize\Serializer\Json;
use \Psr\Log\LoggerInterface;
use Magento\Sales\Model\Order\Email\Container\Template as TemplateContainer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Creditmemo;
use Emartech\Emarsys\Model\Event as EventModel;

/**
 * Class SenderBuilderPlugin
 * @package Emartech\Emarsys\Events
 */
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
     * @var CustomerRepositoryInterface
     */
    private $customerRepositoryInterface;
    /**
     * @var CustomerViewHelper
     */
    private $customerViewHelper;
    /**
     * @var Json
     */
    private $json;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * SenderBuilderPlugin constructor.
     *
     * @param ConfigReader                $configReader
     * @param EmarsysEventFactory         $eventFactory
     * @param EventRepository             $eventRepository
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param CustomerViewHelper          $customerViewHelper
     * @param Json                        $json
     * @param LoggerInterface             $logger
     * @param ContainerBuilder            $containerBuilder
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
     * @param callable      $proceed
     *
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
            $reflection = new \ReflectionClass('\Magento\Sales\Model\Order\Email\SenderBuilder');

            /** @var \Magento\Sales\Model\Order\Email\Container\OrderIdentity $identityContainer */
            $identityContainer = $reflection->getProperty('identityContainer');
            $identityContainer->setAccessible(true);
            $identityContainer = $identityContainer->getValue($senderBuilder);
            $storeId = $identityContainer->getStore()->getStoreId();
            $websiteId = $identityContainer->getStore()->getWebsiteId();

            if (!$this->configReader->isEnabledForStore(ConfigInterface::MARKETING_EVENTS, $storeId)) {
                return $proceed();
            }

            /** @var TemplateContainer $templateContainer */
            $templateContainer = $reflection->getProperty('templateContainer');
            $templateContainer->setAccessible(true);
            $templateContainer = $templateContainer->getValue($senderBuilder);

            $data = $this->parseTemplateVars($templateContainer);
            $data['customerName'] = $identityContainer->getCustomerName();
            $data['customerEmail'] = $identityContainer->getCustomerEmail();
            $data['emailCopyTo'] = $identityContainer->getEmailCopyTo();

            $this->saveEvent(
                $websiteId,
                $storeId,
                $templateContainer->getTemplateId(),
                $data['order']['entity_id'],
                $data
            );
        } catch (\Exception $e) {
            $this->logger->warning('Emartech\\Emarsys\\Events\\SenderBuilderPlugin: ' . $e->getMessage());
        }
    }

    /**
     * @param TemplateContainer $templateContainer
     *
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function parseTemplateVars($templateContainer)
    {
        $returnArray = [];

        foreach ($templateContainer->getTemplateVars() as $key => $value) {
            switch ($key) {
                case 'order':
                    $this->parseOrderVars($key, $value, $returnArray);
                    break;
                case 'invoice':
                    $this->parseInvoiceVars($key, $value, $returnArray);
                    break;
                case 'creditmemo':
                    $this->parseCreditmemoVars($key, $value, $returnArray);
                    break;
            }
        }

        return $returnArray;
    }

    /**
     * @param string $key
     * @param Order  $order
     * @param array  $data
     *
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function parseOrderVars($key, $order, &$data)
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
            /** @var \Magento\Customer\Model\Data\Customer $customer */
            $customer = $this->customerRepositoryInterface->getById($order->getCustomerId());
            $customer->setData('name', $this->customerViewHelper->getCustomerName($customer));
            $data['customer'] = $customer->__toArray();
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
     * @param string  $key
     * @param Invoice $invoice
     * @param array   $data
     *
     * @return void
     */
    private function parseInvoiceVars($key, $invoice, &$data)
    {
        $data[$key] = $invoice->getData();
        $items = [];
        foreach ($invoice->getAllItems() as $item) {
            $items[] = $item->getData();
        }
        $data[$key]['items'] = $items;

        $comments = [];
        foreach ($invoice->getComments() as $comment) {
            $comments[] = $comment->getData();
        }
        $data[$key]['comments'] = $comments;
    }

    /**
     * @param string     $key
     * @param Creditmemo $creditmemo
     * @param array      $data
     *
     * @return void
     */
    private function parseCreditmemoVars($key, $creditmemo, &$data)
    {
        $data[$key] = $creditmemo->getData();
        $items = [];
        foreach ($creditmemo->getAllItems() as $item) {
            $items[] = $item->getData();
        }
        $data[$key]['items'] = $items;

        $comments = [];
        foreach ($creditmemo->getComments() as $comment) {
            $comments[] = $comment->getData();
        }
        $data[$key]['comments'] = $comments;
    }

    /**
     * @param int    $websiteId
     * @param int    $storeId
     * @param string $type
     * @param int    $entityId
     * @param array  $data
     *
     * @return void
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    private function saveEvent($websiteId, $storeId, $type, $entityId, $data)
    {
        $data = $this->json->serialize($data);

        /** @var EventModel $eventModel */
        $eventModel = $this->eventFactory->create()
            ->setEntityId($entityId)
            ->setWebsiteId($websiteId)
            ->setStoreId($storeId)
            ->setEventType($type)
            ->setEventData($data);

        $this->eventRepository->save($eventModel);
    }
}

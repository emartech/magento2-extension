<?php

namespace Emartech\Emarsys\Events;

use Emartech\Emarsys\Api\Data\ConfigInterface;
use Emartech\Emarsys\Helper\ConfigReader;
use Emartech\Emarsys\Helper\Customer as CustomerHelper;
use Emartech\Emarsys\Helper\Json;
use Emartech\Emarsys\Model\EventFactory as EmarsysEventFactory;
use Emartech\Emarsys\Model\EventRepository;
use Magento\Config\Model\ResourceModel\Config as ConfigResource;
use Magento\Framework\App\Config\Initial\Reader as InitialConfigReader;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
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
     * @var ConfigResource
     */
    private $configResource;

    /**
     * @var InitialConfigReader
     */
    private $initialConfigReader;

    /**
     * @var array
     */
    private $initialConfig = [];

    /**
     * SenderBuilderPlugin constructor.
     *
     * @param ConfigReader        $configReader
     * @param InitialConfigReader $initialConfigReader
     * @param EmarsysEventFactory $eventFactory
     * @param EventRepository     $eventRepository
     * @param Json                $json
     * @param CustomerHelper      $customerHelper
     * @param ConfigResource      $configResource
     * @param LoggerInterface     $logger
     */
    public function __construct(
        ConfigReader $configReader,
        InitialConfigReader $initialConfigReader,
        EmarsysEventFactory $eventFactory,
        EventRepository $eventRepository,
        Json $json,
        CustomerHelper $customerHelper,
        ConfigResource $configResource,
        LoggerInterface $logger
    ) {
        $this->configReader = $configReader;
        $this->initialConfigReader = $initialConfigReader;
        $this->eventFactory = $eventFactory;
        $this->eventRepository = $eventRepository;
        $this->json = $json;
        $this->logger = $logger;
        $this->customerHelper = $customerHelper;
        $this->configResource = $configResource;
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
                $this->getOriginalTemplateId($templateContainer->getTemplateId()),
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
        $data = json_encode($data, JSON_INVALID_UTF8_IGNORE);

        $eventModel = $this->eventFactory
            ->create()
            ->setEntityId($entityId)
            ->setWebsiteId($websiteId)
            ->setStoreId($storeId)
            ->setEventType($type)
            ->setEventData($data);

        $this->eventRepository->save($eventModel);
    }

    /**
     * Get config path by template ID
     *
     * @param int $templateId
     *
     * @return string
     * @throws LocalizedException
     */
    private function getConfigPathByTemplateId(string $templateId): string
    {
        $select = $this->configResource->getConnection()->select()
            ->from($this->configResource->getMainTable(), ['path'])
            ->where('value = ?', $templateId)
            ->where('path like ?', '%template')
            ->limit(1);

        return (string) $this->configResource->getConnection()->fetchOne($select);
    }

    /**
     * Get original template ID
     *
     * @param string $templateId
     *
     * @return string
     * @throws LocalizedException
     */
    private function getOriginalTemplateId(string $templateId): string
    {
        if (!is_numeric($templateId)) {
            return $templateId;
        }

        $configPath = $this->getConfigPathByTemplateId($templateId);
        if (!$configPath) {
            return $templateId;
        }

        if (empty($this->initialConfig)) {
            $this->initialConfig = $this->initialConfigReader->read();
        }

        if (isset($this->initialConfig['data']['default'])) {
            $configValue = $this->initialConfig['data']['default'];
            foreach (explode('/', $configPath) as $key) {
                $configValue = $configValue[$key] ?? [];
            }

            if (!empty($configValue) && is_string($configValue)) {
                return (string) $configValue;
            }
        }

        return $templateId;
    }
}

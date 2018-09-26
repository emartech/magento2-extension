<?php

namespace Emartech\Emarsys\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Psr\Log\LoggerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;

use Emartech\Emarsys\Model\EventFactory;
use Emartech\Emarsys\Model\ResourceModel\Event\CollectionFactory as EventCollectionFactory;
use Emartech\Emarsys\Model\ResourceModel\Event\Collection as EventCollection;
use Emartech\Emarsys\Api\EventRepositoryInterface;
use Emartech\Emarsys\Model\Event as EventModel;
use Emartech\Emarsys\Api\Data\ConfigInterface;

/**
 * Class BaseEventHandler
 * @package Emartech\Emarsys\Helper
 */
class BaseEventHandler extends AbstractHelper
{
    /**
     * @var LoggerInterface
     */
    // @codingStandardsIgnoreLine
    protected $logger;

    /**
     * @var ConfigReader
     */
    // @codingStandardsIgnoreLine
    protected $configReader;

    /**
     * @var EventFactory
     */
    // @codingStandardsIgnoreLine
    protected $eventFactory;

    /**
     * @var EventRepositoryInterface
     */
    // @codingStandardsIgnoreLine
    protected $eventRepository;

    /**
     * @var StoreManagerInterface
     */
    // @codingStandardsIgnoreLine
    protected $storeManager;

    /**
     * @var JsonSerializer
     */
    private $jsonSerializer;

    /**
     * @var EventCollectionFactory
     */
    private $eventCollectionFactory;

    /**
     * BaseEventHandler constructor.
     *
     * @param LoggerInterface          $logger
     * @param StoreManagerInterface    $storeManager
     * @param ConfigReader             $configReader
     * @param EventFactory             $eventFactory
     * @param EventRepositoryInterface $eventRepository
     * @param EventCollectionFactory   $eventCollectionFactory
     * @param JsonSerializer           $jsonSerializer
     * @param Context                  $context
     */
    public function __construct(
        LoggerInterface $logger,
        StoreManagerInterface $storeManager,
        ConfigReader $configReader,
        EventFactory $eventFactory,
        EventRepositoryInterface $eventRepository,
        EventCollectionFactory $eventCollectionFactory,
        JsonSerializer $jsonSerializer,
        Context $context
    ) {
        $this->logger = $logger;
        $this->storeManager = $storeManager;
        $this->configReader = $configReader;
        $this->eventFactory = $eventFactory;
        $this->eventRepository = $eventRepository;
        $this->jsonSerializer = $jsonSerializer;
        $this->eventCollectionFactory = $eventCollectionFactory;

        parent::__construct($context);
    }

    /**
     * @param int $websiteId
     *
     * @return bool
     */
    // @codingStandardsIgnoreLine
    protected function isEnabledForWebsite($websiteId)
    {
        return $this->configReader->isEnabledForWebsite(ConfigInterface::CUSTOMER_EVENTS, $websiteId);
    }

    /**
     * @param int $storeId
     *
     * @return bool
     */
    // @codingStandardsIgnoreLine
    protected function isEnabledForStore($storeId)
    {
        return $this->configReader->isEnabledForStore(ConfigInterface::SALES_EVENTS, $storeId);
    }

    /**
     * @param int    $websiteId
     * @param int    $storeId
     * @param string $type
     * @param int    $entityId
     * @param array  $data
     *
     * @return void
     */
    // @codingStandardsIgnoreLine
    protected function saveEvent($websiteId, $storeId, $type, $entityId, $data)
    {
        $this->removeOldEvents($type, $entityId, $storeId);

        $data = $this->jsonSerializer->serialize($data);

        /** @var EventModel $eventModel */
        $eventModel = $this->eventFactory->create()
            ->setEntityId($entityId)
            ->setWebsiteId($websiteId)
            ->setStoreId($storeId)
            ->setEventType($type)
            ->setEventData($data);

        $this->eventRepository->save($eventModel);

        $this->logger->info('event_type: ' . $type . ', event_data: ' . $data);
    }

    /**
     * @param string $type
     * @param int    $entityId
     * @param int    $storeId
     *
     * @return void
     */
    private function removeOldEvents($type, $entityId, $storeId)
    {
        /** @var EventCollection $oldEventCollection */
        $oldEventCollection = $this->eventCollectionFactory->create();

        $oldEventCollection
            ->addFieldToFilter('entity_id', ['eq' => $entityId])
            ->addFieldToFilter('event_type', ['eq' => $type])
            ->addFieldToFilter('store_id', ['eq' => $storeId]);

        $oldEventCollection->walk('delete');
    }
}

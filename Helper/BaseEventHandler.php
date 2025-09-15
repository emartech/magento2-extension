<?php

namespace Emartech\Emarsys\Helper;

use Emartech\Emarsys\Api\Data\ConfigInterface;
use Emartech\Emarsys\Api\EventRepositoryInterface;
use Emartech\Emarsys\Helper\Json as JsonSerializer;
use Emartech\Emarsys\Model\EventFactory;
use Emartech\Emarsys\Model\ResourceModel\Event\CollectionFactory as EventCollectionFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Store\Model\StoreManagerInterface;

class BaseEventHandler extends AbstractHelper
{
    /**
     * @var ConfigReader
     */
    protected $configReader;

    /**
     * @var EventFactory
     */
    protected $eventFactory;

    /**
     * @var EventRepositoryInterface
     */
    protected $eventRepository;

    /**
     * @var StoreManagerInterface
     */
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
     * @param StoreManagerInterface    $storeManager
     * @param ConfigReader             $configReader
     * @param EventFactory             $eventFactory
     * @param EventRepositoryInterface $eventRepository
     * @param EventCollectionFactory   $eventCollectionFactory
     * @param JsonSerializer           $jsonSerializer
     * @param Context                  $context
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ConfigReader $configReader,
        EventFactory $eventFactory,
        EventRepositoryInterface $eventRepository,
        EventCollectionFactory $eventCollectionFactory,
        JsonSerializer $jsonSerializer,
        Context $context
    ) {
        $this->storeManager = $storeManager;
        $this->configReader = $configReader;
        $this->eventFactory = $eventFactory;
        $this->eventRepository = $eventRepository;
        $this->jsonSerializer = $jsonSerializer;
        $this->eventCollectionFactory = $eventCollectionFactory;

        parent::__construct($context);
    }

    /**
     * IsEnabledForWebsite
     *
     * @param int|null $websiteId
     *
     * @return bool
     */
    protected function isEnabledForWebsite(?int $websiteId = null): bool
    {
        return $this->configReader->isEnabledForWebsite(ConfigInterface::CUSTOMER_EVENTS, $websiteId);
    }

    /**
     * IsEnabledForStore
     *
     * @param int|null $storeId
     *
     * @return bool
     */
    protected function isEnabledForStore(?int $storeId = null): bool
    {
        return $this->configReader->isEnabledForStore(ConfigInterface::SALES_EVENTS, $storeId);
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
    protected function saveEvent(int $websiteId, int $storeId, string $type, int $entityId, array $data): void
    {
        $this->removeOldEvents($type, $entityId, $storeId);

        $data = $this->jsonSerializer->serialize($data);

        $eventModel = $this->eventFactory
            ->create()
            ->setEntityId($entityId)
            ->setWebsiteId($websiteId)
            ->setStoreId($storeId)
            ->setEventType($type)
            ->setEventData($data);

        $this->eventRepository->save($eventModel);

        $this->_logger->info('event_type: ' . $type);
    }

    /**
     * RemoveOldEvents
     *
     * @param string $type
     * @param int    $entityId
     * @param int    $storeId
     *
     * @return void
     */
    private function removeOldEvents(string $type, int $entityId, int $storeId): void
    {
        $oldEventCollection = $this->eventCollectionFactory
            ->create()
            ->addFieldToFilter('entity_id', ['eq' => $entityId])
            ->addFieldToFilter('event_type', ['eq' => $type])
            ->addFieldToFilter('store_id', ['eq' => $storeId]);

        $oldEventCollection->walk('delete');
    }
}

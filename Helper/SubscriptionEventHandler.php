<?php

namespace Emartech\Emarsys\Helper;

use Emartech\Emarsys\Api\EventRepositoryInterface;
use Emartech\Emarsys\Helper\Json as JsonSerializer;
use Emartech\Emarsys\Model\EventFactory;
use Emartech\Emarsys\Model\ResourceModel\Event\CollectionFactory as EventCollectionFactory;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Newsletter\Model\Subscriber;
use Magento\Store\Model\StoreManagerInterface;

class SubscriptionEventHandler extends BaseEventHandler
{
    public const DEFAULT_TYPE = 'subscription/unknown';

    /**
     * @var Subscriber
     */
    private $subscriber;

    /**
     * SubscriptionEventHandler constructor.
     *
     * @param Subscriber               $subscriber
     * @param ConfigReader             $configReader
     * @param EventFactory             $eventFactory
     * @param EventRepositoryInterface $eventRepository
     * @param EventCollectionFactory   $eventCollectionFactory
     * @param Context                  $context
     * @param StoreManagerInterface    $storeManager
     * @param JsonSerializer           $jsonSerializer
     */
    public function __construct(
        Subscriber $subscriber,
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

        $this->subscriber = $subscriber;
    }

    /**
     * Store
     *
     * @param Subscriber  $subscription
     * @param int|null    $websiteId
     * @param int|null    $storeId
     * @param string|null $type
     *
     * @return bool
     * @throws AlreadyExistsException
     */
    public function store(
        Subscriber $subscription,
        ?int $websiteId = null,
        ?int $storeId = null,
        ?string $type = null
    ): bool {
        if (!$this->isEnabledForWebsite($websiteId)) {
            return false;
        }

        if (!$type) {
            $type = self::DEFAULT_TYPE;
        }

        $eventData = $subscription->getData();

        $this->saveEvent($websiteId, $storeId, $type, $subscription->getId(), $eventData);

        return true;
    }

    /**
     * GetEventType
     *
     * @param string $eventName
     *
     * @return string
     */
    public function getEventType(string $eventName): string
    {
        switch ($eventName) {
            case 'newsletter_subscriber_save_after':
                $returnType = 'subscription/update';
                break;
            case 'newsletter_subscriber_delete_after':
                $returnType = 'subscription/delete';
                break;
            default:
                $returnType = self::DEFAULT_TYPE;
        }

        return $returnType;
    }
}

<?php

namespace Emartech\Emarsys\Helper;

use Magento\Newsletter\Model\Subscriber;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;

use Emartech\Emarsys\Model\EventFactory;
use Emartech\Emarsys\Model\ResourceModel\Event\CollectionFactory as EventCollectionFactory;
use Emartech\Emarsys\Api\EventRepositoryInterface;

/**
 * Class SubscriptionEventHandler
 * @package Emartech\Emarsys\Helper
 */
class SubscriptionEventHandler extends BaseEventHandler
{
    const DEFAULT_TYPE = 'subscription/unknown';

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
     * @param LoggerInterface          $logger
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
        LoggerInterface $logger,
        StoreManagerInterface $storeManager,
        JsonSerializer $jsonSerializer
    ) {
        parent::__construct(
            $logger,
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
     * @param Subscriber  $subscription
     * @param int         $websiteId
     * @param int         $storeId
     * @param null|string $type
     *
     * @return bool
     */
    public function store(Subscriber $subscription, $websiteId, $storeId, $type = null)
    {
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
     * @param string $eventName
     *
     * @return string
     */
    public function getEventType($eventName)
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

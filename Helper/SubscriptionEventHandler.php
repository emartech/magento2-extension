<?php


namespace Emartech\Emarsys\Helper;


use Emartech\Emarsys\Api\Data\ConfigInterface;
use Emartech\Emarsys\Model\ResourceModel\Event;
use Emartech\Emarsys\Model\EventFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Newsletter\Model\Subscriber;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class SubscriptionEventHandler extends AbstractHelper
{
  protected $logger;
  private $customerFactory;
  protected $eventFactory;
  protected $eventResource;
  /** @var ConfigReader */
  protected $configReader;
  /** @var StoreManagerInterface */
  protected $storeManager;

  public function __construct(
    ConfigReader $configReader,
    StoreManagerInterface $storeManager,
    CustomerFactory $customerFactory,
    EventFactory $eventFactory,
    Event $eventResource,
    LoggerInterface $logger
  )
  {
    $this->customerFactory = $customerFactory;
    $this->eventFactory = $eventFactory;
    $this->eventResource = $eventResource;
    $this->logger = $logger;
    $this->configReader = $configReader;
    $this->storeManager = $storeManager;
  }

  /**
   * @param $subscription
   * @param $eventName
   * @throws \Exception
   * @throws \Magento\Framework\Exception\AlreadyExistsException
   */
  public function store(Subscriber $subscription, $eventName)
  {
    $websiteId = $this->storeManager->getStore($subscription->getStoreId())->getWebsiteId();

    if (!$this->configReader->isEnabledForWebsite(ConfigInterface::CUSTOMER_EVENTS, $websiteId)) return;

    $eventData = $subscription->getData();

    $eventType = $this->getEventType($eventName);

    /** @var \Emartech\Emarsys\Model\Event $eventModel */
    $eventModel = $this->eventFactory->create();
    $eventModel->setData('event_type', $eventType);
    $eventModel->setData('event_data', json_encode($eventData));
    $this->eventResource->save($eventModel);

    $this->logger->info('event_type: '. $eventType . ', event_data: '.json_encode($eventData));
  }

  /**
   * @param $eventName
   * @return string
   */
  private function getEventType($eventName)
  {
    $eventType = 'subscription/unknown';

    if ($eventName === 'newsletter_subscriber_save_after') {
      $eventType = 'subscription/update';
    } elseif ($eventName === 'newsletter_subscriber_delete_after') {
      $eventType = 'subscription/delete';
    }

    return $eventType;
  }
}
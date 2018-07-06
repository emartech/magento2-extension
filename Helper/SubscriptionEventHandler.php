<?php


namespace Emartech\Emarsys\Helper;


use Emartech\Emarsys\Model\ResourceModel\Event;
use Emartech\Emarsys\Model\EventFactory;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Newsletter\Model\Subscriber;
use Psr\Log\LoggerInterface;

class SubscriptionEventHandler extends AbstractHelper
{
  protected $logger;
  private $customerFactory;
  protected $eventFactory;
  protected $eventResource;
  private $subscriber;
  /**
   * @var Data
   */
  private $emarsysData;

  public function __construct(
    Data $emarsysData,
    CustomerFactory $customerFactory,
    EventFactory $eventFactory,
    Event $eventResource,
    Subscriber $subscriber,
    LoggerInterface $logger
  )
  {
    $this->customerFactory = $customerFactory;
    $this->eventFactory = $eventFactory;
    $this->eventResource = $eventResource;
    $this->logger = $logger;
    $this->subscriber = $subscriber;
    $this->emarsysData = $emarsysData;
  }

  /**
   * @param $subscription
   * @throws \Exception
   * @throws \Magento\Framework\Exception\AlreadyExistsException
   */
  public function store($subscription)
  {
    if (!$this->emarsysData->isEnabled(Data::CUSTOMER_EVENTS)) return;

    $eventData = $subscription->getData();

    /** @var \Emartech\Emarsys\Model\Event $eventModel */
    $eventModel = $this->eventFactory->create();
    $eventModel->setData('event_type', 'subscription/update');
    $eventModel->setData('event_data', json_encode($eventData));
    $this->eventResource->save($eventModel);

    $this->logger->info('event_type: '. 'subscription/update' . ', event_data: '.json_encode($eventData));
  }
}
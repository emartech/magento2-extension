<?php


namespace Emartech\Emarsys\Helper;


use Emartech\Emarsys\Api\Data\ConfigInterface;
use Emartech\Emarsys\Model\ResourceModel\Event;
use Emartech\Emarsys\Model\EventFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Newsletter\Model\Subscriber;
use Psr\Log\LoggerInterface;

class EmailEventHandler extends AbstractHelper
{
  protected $logger;
  protected $customerFactory;
  protected $eventFactory;
  protected $eventResource;
  protected $subscriber;
  protected $emarsysData;
  /** @var ConfigReader */
  protected $configReader;

  public function __construct(
    ConfigReader $configReader,
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

    $this->configReader = $configReader;
  }

  /**
   * @param $template
   * @param $args
   * @throws \Exception
   * @throws \Magento\Framework\Exception\AlreadyExistsException
   */
  public function store($template, $args)
  {
    if (!$this->configReader->isEnabledForWebsite(ConfigInterface::MARKETING_EVENTS)) return;

    $emailData = [
      'customer' => $args[0]['customer']->getData(),
      'store' => $args[0]['store']->getData(),
      'url'=> $args[0]['back_url']
    ];


    /** @var \Emartech\Emarsys\Model\Event $eventModel */
    $eventModel = $this->eventFactory->create();
    $eventModel->setEventType($template);
    $eventModel->setEventData(json_encode($emailData));
    $this->eventResource->save($eventModel);

    $this->logger->info('event_type: '. $template . ', event_data: '.json_encode($emailData));
  }
}
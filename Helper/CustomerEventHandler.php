<?php


namespace Emartech\Emarsys\Helper;


use Emartech\Emarsys\Model\ResourceModel\Event;
use Emartech\Emarsys\Model\SettingsFactory;
use Emartech\Emarsys\Model\EventFactory;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Newsletter\Model\Subscriber;
use Psr\Log\LoggerInterface;

class CustomerEventHandler extends AbstractHelper
{
  protected $logger;
  private $customerFactory;
  protected $eventFactory;
  protected $eventResource;
  private $subscriber;
  private $settings;

  public function __construct(
    SettingsFactory $settingsFactory,
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

    $this->getSettings($settingsFactory);
  }

  /**
   * @param string $type
   * @param int $customerId
   * @throws \Exception
   * @throws \Magento\Framework\Exception\AlreadyExistsException
   */
  public function store($type, $customerId)
  {
    if (!$this->settings['collectCustomerEvents']) return;

    /** @var Customer $customer */
    $customer = $this->customerFactory->create()->load($customerId);
    $customerData = $customer->toArray();

    if ($customer->getDefaultBillingAddress()) {
      $customerData['billing_address'] = $customer->getDefaultBillingAddress()->toArray();
    }
    if ($customer->getDefaultShippingAddress()) {
      $customerData['shipping_address'] = $customer->getDefaultShippingAddress()->toArray();
    }

    $subscription = $this->subscriber->loadByCustomerId($customerId);
    $customerData['subscription'] = $subscription->getStatus();

    /** @var \Emartech\Emarsys\Model\Event $eventModel */
    $eventModel = $this->eventFactory->create();
    $eventModel->setData('event_type', $type);
    $eventModel->setData('event_data', json_encode($customerData));
    $this->eventResource->save($eventModel);

    $this->logger->info('event_type: '. $type . ', event_data: '.json_encode($customerData));
  }

  /**
   * @param SettingsFactory $settingsFactory
   */
  private function getSettings(SettingsFactory $settingsFactory)
  {
    $settingsResource = $settingsFactory->create();
    $settings = $settingsResource->getCollection();
    foreach ($settings as $setting) {
      $name = $setting->getSetting();
      $value = $setting->getValue() === 'enabled' ? true : false;
      $this->settings[$name] = $value;
    }
  }
}
<?php

namespace Emartech\Emarsys\Events;

use Emartech\Emarsys\Api\Data\ConfigInterface;
use Emartech\Emarsys\Helper\ConfigReader;
use Emartech\Emarsys\Model\EventRepository;
use Emartech\Emarsys\Model\SettingsFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Customer\Helper\View as CustomerViewHelper;
use \Psr\Log\LoggerInterface;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\CustomerFactory;
use Magento\Newsletter\Model\Subscriber;
use Emartech\Emarsys\Model\EventFactory as EmarsysEventFactory;
use Emartech\Emarsys\Model\Event as EventModel;
use Magento\Customer\Model\EmailNotificationInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Data\CustomerSecure;

use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class CustomerPlugin
 * @package Emartech\Emarsys\Events
 */
class CustomerPlugin
{
    const EVENT_NEWSLETTER_SEND_CONFIRMATION_SUCCESS_EMAIL = 'newsletter_send_confirmation_success_email';
    const EVENT_NEWSLETTER_SEND_CONFIRMATION_REQUEST_EMAIL = 'newsletter_send_confirmation_request_email';
    const EVENT_NEWSLETTER_SEND_UNSUBSCRIPTION_EMAIL       = 'newsletter_send_unsubscription_email';
    const EVENT_CUSTOMER_NEW_ACCOUNT                       = 'customer_new_account_';
    const EVENT_CUSTOMER_EMAIL_AND_PASSWORD_CHANGED        = 'customer_email_and_password_changed';
    const EVENT_CUSTOMER_EMAIL_CHANGED                     = 'customer_email_changed';
    const EVENT_CUSTOMER_PASSWORD_RESET                    = 'customer_password_reset';
    const EVENT_CUSTOMER_PASSWORD_REMINDER                 = 'customer_password_reminder';
    const EVENT_CUSTOMER_PASSWORD_RESET_CONFIRMATION       = 'customer_password_reset_confirmation';

    /**
     * @var ScopeConfigInterface
     */
    public $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    public $storeManager;

    /**
     * @var LoggerInterface
     */
    public $logger;

    /**
     * @var EventRepository
     */
    public $eventRepository;

    /**
     * @var CustomerRegistry
     */
    public $customerRegistry;

    /**
     * @var EmarsysEventFactory
     */
    public $eventFactory;

    /**
     * @var DataObjectProcessor
     */
    public $dataProcessor;

    /**
     * @var CustomerViewHelper
     */
    public $customerViewHelper;

    /**
     * @var Json
     */
    public $json;

    /**
     * @var ConfigReader
     */
    public $configReader;

    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * CustomerPlugin constructor.
     *
     * @param ScopeConfigInterface  $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface       $logger
     * @param EventRepository       $eventRepository
     * @param CustomerRegistry      $customerRegistry
     * @param CustomerFactory       $customerFactory
     * @param EmarsysEventFactory   $eventFactory
     * @param DataObjectProcessor   $dataProcessor
     * @param CustomerViewHelper    $customerViewHelper
     * @param ConfigReader          $configReader
     * @param Json                  $json
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger,
        EventRepository $eventRepository,
        CustomerRegistry $customerRegistry,
        CustomerFactory $customerFactory,
        EmarsysEventFactory $eventFactory,
        DataObjectProcessor $dataProcessor,
        CustomerViewHelper $customerViewHelper,
        ConfigReader $configReader,
        Json $json
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->eventRepository = $eventRepository;
        $this->customerRegistry = $customerRegistry;
        $this->eventFactory = $eventFactory;
        $this->dataProcessor = $dataProcessor;
        $this->customerViewHelper = $customerViewHelper;
        $this->json = $json;
        $this->configReader = $configReader;
        $this->customerFactory = $customerFactory;
    }

    /**
     * @param Subscriber $subscriber
     * @param callable   $proceed
     *
     * @return mixed
     */
    public function aroundSendConfirmationSuccessEmail(
        Subscriber $subscriber,
        callable $proceed
    ) {
        if (!$this->handleConfirmation($subscriber, self::EVENT_NEWSLETTER_SEND_CONFIRMATION_SUCCESS_EMAIL)) {
            return $proceed($subscriber);
        }
    }

    /**
     * @param Subscriber $subscriber
     * @param callable   $proceed
     *
     * @return mixed
     */
    public function aroundSendConfirmationRequestEmail(
        Subscriber $subscriber,
        callable $proceed
    ) {
        if (!$this->handleConfirmation($subscriber, self::EVENT_NEWSLETTER_SEND_CONFIRMATION_REQUEST_EMAIL)) {
            return $proceed($subscriber);
        }
    }

    /**
     * @param Subscriber $subscriber
     * @param callable   $proceed
     *
     * @return mixed
     */
    public function aroundSendUnsubscriptionEmail(
        Subscriber $subscriber,
        callable $proceed
    ) {
        if (!$this->handleConfirmation($subscriber, self::EVENT_NEWSLETTER_SEND_UNSUBSCRIPTION_EMAIL)) {
            return $proceed($subscriber);
        }
    }

    /**
     * @param EmailNotificationInterface $emailNotification
     * @param callable                   $proceed
     * @param CustomerInterface          $customer
     * @param string                     $type
     * @param string                     $backUrl
     * @param int                        $storeId
     * @param null|int                   $senderMailStoreId
     *
     * @return mixed
     * @throws AlreadyExistsException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function aroundNewAccount(
        EmailNotificationInterface $emailNotification,
        callable $proceed,
        CustomerInterface $customer,
        $type = EmailNotificationInterface::NEW_ACCOUNT_EMAIL_REGISTERED,
        $backUrl = '',
        $storeId = 0,
        $senderMailStoreId = null
    ) {
        if (!$storeId) {
            $storeId = $this->getWebsiteStoreId($customer, $senderMailStoreId);
        }
        $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();

        if (!$this->configReader->isEnabledForWebsite(ConfigInterface::MARKETING_EVENTS, $websiteId)) {
            return $proceed($customer, $type, $backUrl, $storeId, $senderMailStoreId);
        }

        $this->saveEvent(
            $websiteId,
            $storeId,
            self::EVENT_CUSTOMER_NEW_ACCOUNT . $type,
            $customer->getId(),
            [
                'customer' => $this->getFullCustomerObject($customer)->getData(),
                'back_url' => $backUrl,
                'store'    => $this->storeManager->getStore($storeId)->getData(),
            ]
        );
    }

    /**
     * @param EmailNotificationInterface $emailNotification
     * @param callable                   $proceed
     * @param CustomerInterface          $savedCustomer
     * @param string                     $origCustomerEmail
     * @param bool                       $isPasswordChanged
     *
     * @return mixed
     * @throws AlreadyExistsException
     * @throws NoSuchEntityException
     */
    public function aroundCredentialsChanged(
        EmailNotificationInterface $emailNotification,
        callable $proceed,
        CustomerInterface $savedCustomer,
        $origCustomerEmail,
        $isPasswordChanged = false
    ) {
        $websiteId = $savedCustomer->getWebsiteId();
        $storeId = $savedCustomer->getStoreId();

        if (!$this->configReader->isEnabledForWebsite(ConfigInterface::MARKETING_EVENTS, $websiteId)) {
            return $proceed($savedCustomer, $origCustomerEmail, $isPasswordChanged);
        }

        $store = $this->storeManager->getStore($savedCustomer->getStoreId());

        $eventData = [
            'customer'            => $this->getFullCustomerObject($savedCustomer)->getData(),
            'store'               => $store->getData(),
            'orig_customer_email' => $origCustomerEmail,
            'new_customer_email'  => $savedCustomer->getEmail(),
        ];

        if ($origCustomerEmail !== $savedCustomer->getEmail()) {
            if ($isPasswordChanged) {
                $this->saveEvent(
                    $websiteId,
                    $storeId,
                    self::EVENT_CUSTOMER_EMAIL_AND_PASSWORD_CHANGED,
                    $savedCustomer->getId(),
                    $eventData
                );
            } else {
                $this->saveEvent(
                    $websiteId,
                    $storeId,
                    self::EVENT_CUSTOMER_EMAIL_CHANGED,
                    $savedCustomer->getId(),
                    $eventData
                );
            }
        } elseif ($isPasswordChanged) {
            $this->saveEvent(
                $websiteId,
                $storeId,
                self::EVENT_CUSTOMER_PASSWORD_RESET,
                $savedCustomer->getId(),
                $eventData
            );
        }
    }

    /**
     * @param EmailNotificationInterface $emailNotification
     * @param callable                   $proceed
     * @param CustomerInterface          $customer
     *
     * @return mixed
     * @throws AlreadyExistsException
     * @throws NoSuchEntityException
     */
    public function aroundPasswordReminder(
        EmailNotificationInterface $emailNotification,
        callable $proceed,
        CustomerInterface $customer
    ) {
        $websiteId = $customer->getWebsiteId();
        $storeId = $customer->getStoreId();

        if (!$this->configReader->isEnabledForWebsite(ConfigInterface::MARKETING_EVENTS, $websiteId)) {
            return $proceed($customer);
        }

        $store = $this->storeManager->getStore($storeId);

        $this->saveEvent(
            $websiteId,
            $storeId,
            self::EVENT_CUSTOMER_PASSWORD_REMINDER,
            $customer->getId(),
            [
                'customer' => $this->getFullCustomerObject($customer)->getData(),
                $store->getData(),
            ]
        );
    }

    /**
     * @param EmailNotificationInterface $emailNotification
     * @param callable                   $proceed
     * @param CustomerInterface          $customer
     *
     * @return mixed
     * @throws AlreadyExistsException
     * @throws NoSuchEntityException
     */
    public function aroundPasswordResetConfirmation(
        EmailNotificationInterface $emailNotification,
        callable $proceed,
        CustomerInterface $customer
    ) {
        $websiteId = $customer->getWebsiteId();
        $storeId = $customer->getStoreId();

        if (!$this->configReader->isEnabledForWebsite(ConfigInterface::MARKETING_EVENTS, $websiteId)) {
            return $proceed($customer);
        }

        $store = $this->storeManager->getStore($storeId);

        $this->saveEvent(
            $websiteId,
            $storeId,
            self::EVENT_CUSTOMER_PASSWORD_RESET_CONFIRMATION,
            $customer->getId(),
            [
                'customer' => $this->getFullCustomerObject($customer)->getData(),
                $store->getData(),
            ]
        );
    }

    /**
     * @param CustomerInterface $customer
     * @param null              $defaultStoreId
     *
     * @return int
     * @throws LocalizedException
     */
    private function getWebsiteStoreId($customer, $defaultStoreId = null)
    {
        if ($customer->getWebsiteId() != 0 && empty($defaultStoreId)) {
            $storeIds = $this->storeManager->getWebsite($customer->getWebsiteId())->getStoreIds();
            $defaultStoreId = reset($storeIds);
        }
        return $defaultStoreId;
    }

    /**
     * @param CustomerInterface $customer
     *
     * @return CustomerSecure
     * @throws NoSuchEntityException
     */
    private function getFullCustomerObject($customer)
    {
        // No need to flatten the custom attributes or nested objects since the only usage is for email templates and
        // object passed for events
        $mergedCustomerData = $this->customerRegistry->retrieveSecureData($customer->getId());
        $customerData = $this->dataProcessor
            ->buildOutputDataArray($customer, \Magento\Customer\Api\Data\CustomerInterface::class);
        $mergedCustomerData->addData($customerData);

        return $mergedCustomerData;
    }

    /**
     * @param Subscriber $subscriber
     *
     * @return array
     */
    private function getDataFromSubscription(Subscriber $subscriber)
    {
        $data = [
            'subscriber' => $subscriber->getData(),
        ];

        if ($subscriber->getConfirmationLink()) {
            $data['subscriber'] = $subscriber->getData();
        }
        if ($subscriber->getCustomerId()) {
            $customer = $this->customerFactory->create()->load($subscriber->getCustomerId());
            $data['customer'] = $customer->getData();
        }
        return $data;
    }

    /**
     * @param Subscriber $subscriber
     * @param string     $type
     *
     * @return bool
     */
    private function handleConfirmation($subscriber, $type)
    {
        $storeId = $subscriber->getStoreId();
        try {
            $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();
        } catch (\Exception $e) {
            return false;
        }

        if (!$this->configReader->isEnabledForWebsite(ConfigInterface::MARKETING_EVENTS, $websiteId)) {
            return false;
        }

        try {
            $this->saveEvent(
                $websiteId,
                $storeId,
                $type,
                $subscriber->getId(),
                $this->getDataFromSubscription($subscriber)
            );
        } catch (\Exception $e) {
            return false;
        }

        return true;
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

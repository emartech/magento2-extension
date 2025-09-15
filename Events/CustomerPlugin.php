<?php

namespace Emartech\Emarsys\Events;

use Emartech\Emarsys\Api\Data\ConfigInterface;
use Emartech\Emarsys\Helper\ConfigReader;
use Emartech\Emarsys\Helper\Customer as CustomerHelper;
use Emartech\Emarsys\Helper\Json;
use Emartech\Emarsys\Model\EventFactory as EmarsysEventFactory;
use Emartech\Emarsys\Model\EventRepository;
use Exception;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\EmailNotificationInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Newsletter\Model\Subscriber;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class CustomerPlugin
{
    public const EVENT_NEWSLETTER_SEND_CONFIRMATION_SUCCESS_EMAIL = 'newsletter_send_confirmation_success_email';
    public const EVENT_NEWSLETTER_SEND_CONFIRMATION_REQUEST_EMAIL = 'newsletter_send_confirmation_request_email';
    public const EVENT_NEWSLETTER_SEND_UNSUBSCRIPTION_EMAIL       = 'newsletter_send_unsubscription_email';
    public const EVENT_CUSTOMER_NEW_ACCOUNT                       = 'customer_new_account_';
    public const EVENT_CUSTOMER_EMAIL_AND_PASSWORD_CHANGED        = 'customer_email_and_password_changed';
    public const EVENT_CUSTOMER_EMAIL_CHANGED                     = 'customer_email_changed';
    public const EVENT_CUSTOMER_PASSWORD_RESET                    = 'customer_password_reset';
    public const EVENT_CUSTOMER_PASSWORD_REMINDER                 = 'customer_password_reminder';
    public const EVENT_CUSTOMER_PASSWORD_RESET_CONFIRMATION       = 'customer_password_reset_confirmation';

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var EventRepository
     */
    private $eventRepository;

    /**
     * @var EmarsysEventFactory
     */
    private $eventFactory;

    /**
     * @var Json
     */
    private $json;

    /**
     * @var ConfigReader
     */
    private $configReader;

    /**
     * @var CustomerHelper
     */
    private $customerHelper;

    /**
     * CustomerPlugin constructor.
     *
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface       $logger
     * @param EventRepository       $eventRepository
     * @param EmarsysEventFactory   $eventFactory
     * @param ConfigReader          $configReader
     * @param Json                  $json
     * @param CustomerHelper        $customerHelper
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        LoggerInterface $logger,
        EventRepository $eventRepository,
        EmarsysEventFactory $eventFactory,
        ConfigReader $configReader,
        Json $json,
        CustomerHelper $customerHelper
    ) {
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->eventRepository = $eventRepository;
        $this->eventFactory = $eventFactory;
        $this->json = $json;
        $this->configReader = $configReader;
        $this->customerHelper = $customerHelper;
    }

    /**
     * AroundSendConfirmationSuccessEmail
     *
     * @param Subscriber $subscriber
     * @param callable   $proceed
     *
     * @return mixed
     */
    public function aroundSendConfirmationSuccessEmail(
        Subscriber $subscriber,
        callable $proceed
    ) {
        return $this->handleConfirmation($subscriber, self::EVENT_NEWSLETTER_SEND_CONFIRMATION_SUCCESS_EMAIL, $proceed);
    }

    /**
     * AroundSendConfirmationRequestEmail
     *
     * @param Subscriber $subscriber
     * @param callable   $proceed
     *
     * @return mixed
     */
    public function aroundSendConfirmationRequestEmail(
        Subscriber $subscriber,
        callable $proceed
    ) {
        return $this->handleConfirmation($subscriber, self::EVENT_NEWSLETTER_SEND_CONFIRMATION_REQUEST_EMAIL, $proceed);
    }

    /**
     * AroundSendUnsubscriptionEmail
     *
     * @param Subscriber $subscriber
     * @param callable   $proceed
     *
     * @return mixed
     */
    public function aroundSendUnsubscriptionEmail(
        Subscriber $subscriber,
        callable $proceed
    ) {
        return $this->handleConfirmation($subscriber, self::EVENT_NEWSLETTER_SEND_UNSUBSCRIPTION_EMAIL, $proceed);
    }

    /**
     * AroundNewAccount
     *
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
        string $type = EmailNotificationInterface::NEW_ACCOUNT_EMAIL_REGISTERED,
        $backUrl = '',
        int $storeId = 0,
        ?int $senderMailStoreId = null
    ) {
        if (!$storeId) {
            $storeId = $this->getWebsiteStoreId($customer, $senderMailStoreId);
        }

        $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();

        $marketingEventsEnabled = $this->configReader
            ->isEnabledForWebsite(ConfigInterface::MARKETING_EVENTS, $websiteId);
        $magentoSendEmailEnabled = $this->configReader
            ->isEnabledForWebsite(ConfigInterface::MAGENTO_SEND_EMAIL, $websiteId);

        if ($marketingEventsEnabled) {
            $customerData = $this->customerHelper->getOneCustomer($customer->getId(), $websiteId, true);

            if (false !== $customerData) {
                $this->saveEvent(
                    $websiteId,
                    $storeId,
                    self::EVENT_CUSTOMER_NEW_ACCOUNT . $type,
                    $customer->getId(),
                    [
                        'customer' => $customerData,
                        'back_url' => $backUrl,
                        'store'    => $this->storeManager->getStore($storeId)->getData(),
                    ]
                );
            }

            if ($magentoSendEmailEnabled) {
                return $proceed($customer, $type, $backUrl, $storeId, $senderMailStoreId);
            }
        } else {
            return $proceed($customer, $type, $backUrl, $storeId, $senderMailStoreId);
        }
    }

    /**
     * AroundCredentialsChanged
     *
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
        string $origCustomerEmail,
        bool $isPasswordChanged = false
    ) {
        $websiteId = $savedCustomer->getWebsiteId();
        $storeId = $savedCustomer->getStoreId();

        $marketingEventsEnabled = $this->configReader
            ->isEnabledForWebsite(ConfigInterface::MARKETING_EVENTS, $websiteId);
        $magentoSendEmailEnabled = $this->configReader
            ->isEnabledForWebsite(ConfigInterface::MAGENTO_SEND_EMAIL, $websiteId);

        if ($marketingEventsEnabled) {
            $store = $this->storeManager->getStore($storeId);

            $customerData = $this->customerHelper->getOneCustomer($savedCustomer->getId(), $websiteId, true);

            $eventData = [
                'customer'            => $customerData,
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

            if ($magentoSendEmailEnabled) {
                return $proceed($savedCustomer, $origCustomerEmail, $isPasswordChanged);
            }
        } else {
            return $proceed($savedCustomer, $origCustomerEmail, $isPasswordChanged);
        }
    }

    /**
     * AroundPasswordReminder
     *
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

        $marketingEventsEnabled = $this->configReader
            ->isEnabledForWebsite(ConfigInterface::MARKETING_EVENTS, $websiteId);
        $magentoSendEmailEnabled = $this->configReader
            ->isEnabledForWebsite(ConfigInterface::MAGENTO_SEND_EMAIL, $websiteId);

        if ($marketingEventsEnabled) {
            $store = $this->storeManager->getStore($storeId);

            $customerData = $this->customerHelper->getOneCustomer($customer->getId(), $websiteId, true);

            if (false !== $customerData) {
                $this->saveEvent(
                    $websiteId,
                    $storeId,
                    self::EVENT_CUSTOMER_PASSWORD_REMINDER,
                    $customer->getId(),
                    [
                        'customer' => $customerData,
                        'store'    => $store->getData(),
                    ]
                );
            }

            if ($magentoSendEmailEnabled) {
                return $proceed($customer);
            }
        } else {
            return $proceed($customer);
        }
    }

    /**
     * AroundPasswordResetConfirmation
     *
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

        $marketingEventsEnabled = $this->configReader
            ->isEnabledForWebsite(ConfigInterface::MARKETING_EVENTS, $websiteId);
        $magentoSendEmailEnabled = $this->configReader
            ->isEnabledForWebsite(ConfigInterface::MAGENTO_SEND_EMAIL, $websiteId);

        if ($marketingEventsEnabled) {
            $store = $this->storeManager->getStore($storeId);

            $customerData = $this->customerHelper->getOneCustomer($customer->getId(), $websiteId, true);

            if (null !== $customerData) {
                $this->saveEvent(
                    $websiteId,
                    $storeId,
                    self::EVENT_CUSTOMER_PASSWORD_RESET_CONFIRMATION,
                    $customer->getId(),
                    [
                        'customer' => $customerData,
                        'store'    => $store->getData(),
                    ]
                );
            }

            if ($magentoSendEmailEnabled) {
                return $proceed($customer);
            }
        } else {
            return $proceed($customer);
        }
    }

    /**
     * GetWebsiteStoreId
     *
     * @param CustomerInterface $customer
     * @param null              $defaultStoreId
     *
     * @return int
     * @throws LocalizedException
     */
    private function getWebsiteStoreId(CustomerInterface $customer, $defaultStoreId = null): int
    {
        if ($customer->getWebsiteId() != 0 && empty($defaultStoreId)) {
            $storeIds = $this->storeManager->getWebsite($customer->getWebsiteId())->getStoreIds();
            $defaultStoreId = reset($storeIds);
        }

        return $defaultStoreId;
    }

    /**
     * GetDataFromSubscription
     *
     * @param Subscriber $subscriber
     * @param int        $websiteId
     *
     * @return array
     */
    private function getDataFromSubscription(Subscriber $subscriber, int $websiteId): array
    {
        $data = [
            'subscriber' => $subscriber->getData(),
        ];

        if ($subscriber->getConfirmationLink()) {
            $data['subscriber'] = $subscriber->getData();
        }
        if ($subscriber->getCustomerId()) {
            $customerData = $this->customerHelper->getOneCustomer($subscriber->getCustomerId(), $websiteId, true);
            $data['customer'] = false;
            if (null !== $customerData) {
                $data['customer'] = $customerData;
            }
        }

        return $data;
    }

    /**
     * HandleConfirmation
     *
     * @param Subscriber $subscriber
     * @param string     $type
     * @param callable   $proceed
     *
     * @return mixed
     */
    private function handleConfirmation(Subscriber $subscriber, string $type, callable $proceed)
    {
        $storeId = $subscriber->getStoreId();

        try {
            $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();
        } catch (Exception $e) {
            $websiteId = null;
        }

        $marketingEventsEnabled = $this->configReader
            ->isEnabledForWebsite(ConfigInterface::MARKETING_EVENTS, $websiteId);
        $magentoSendEmailEnabled = $this->configReader
            ->isEnabledForWebsite(ConfigInterface::MAGENTO_SEND_EMAIL, $websiteId);

        if ($marketingEventsEnabled) {
            try {
                $this->saveEvent(
                    $websiteId,
                    $storeId,
                    $type,
                    $subscriber->getId(),
                    $this->getDataFromSubscription($subscriber, $websiteId)
                );
            } catch (Exception $e) {
                $marketingEventsEnabled = true;
            }

            if ($magentoSendEmailEnabled) {
                return $proceed($subscriber);
            }
        } else {
            return $proceed($subscriber);
        }
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
        $data = $this->json->serialize($data);

        $eventModel = $this->eventFactory
            ->create()
            ->setEntityId($entityId)
            ->setWebsiteId($websiteId)
            ->setStoreId($storeId)
            ->setEventType($type)
            ->setEventData($data);

        $this->eventRepository->save($eventModel);
    }
}

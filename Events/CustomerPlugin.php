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
use Emartech\Emarsys\Model\EventFactory as EmarsysEventFactory;

/**
 * Customer Events
 */
class CustomerPlugin
{
    const EVENT_NEWSLETTER_SEND_CONFIRMATION_SUCCESS_EMAIL = 'newsletter_send_confirmation_success_email';
    const EVENT_NEWSLETTER_SEND_CONFIRMATION_REQUEST_EMAIL = 'newsletter_send_confirmation_request_email';
    const EVENT_NEWSLETTER_SEND_UNSUBSCRIPTION_EMAIL = 'newsletter_send_unsubscription_email';
    const EVENT_CUSTOMER_NEW_ACCOUNT = 'customer_new_account_';
    const EVENT_CUSTOMER_EMAIL_AND_PASSWORD_CHANGED = 'customer_email_and_password_changed';
    const EVENT_CUSTOMER_EMAIL_CHANGED = 'customer_email_changed';
    const EVENT_CUSTOMER_PASSWORD_RESET = 'customer_password_reset';
    const EVENT_CUSTOMER_PASSWORD_REMINDER = 'customer_password_reminder';
    const EVENT_CUSTOMER_PASSWORD_RESET_CONFIRMATION = 'customer_password_reset_confirmation';

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
     * CustomerPlugin constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     * @param EventRepository $eventRepository
     * @param CustomerRegistry $customerRegistry
     * @param EmarsysEventFactory $eventFactory
     * @param DataObjectProcessor $dataProcessor
     * @param CustomerViewHelper $customerViewHelper
     * @param ConfigReader $configReader
     * @param Json $json
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger,
        EventRepository $eventRepository,
        CustomerRegistry $customerRegistry,
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
    }

    /**
     * @param \Magento\Newsletter\Model\Subscriber $subscriber
     * @param callable $proceed
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function aroundSendConfirmationSuccessEmail(
        \Magento\Newsletter\Model\Subscriber $subscriber,
        callable $proceed
    ) {
        if (!$this->configReader->isEnabled(ConfigInterface::MARKETING_EVENTS)) {
            return $proceed($subscriber);
        }

        $storeId = $subscriber->getStoreId();
        //add config later
        /*if (! $this->scopeConfig->getValue(
            path_in_the_config_table,
            'store',
            $storeId
        )
        ) {
            return $proceed();
        }*/
        /** @var \Emartech\Emarsys\Model\Event $eventModel */
        $eventModel = $this->eventFactory->create();
        $eventModel->setEventType(self::EVENT_NEWSLETTER_SEND_CONFIRMATION_SUCCESS_EMAIL);

        $data = [
            'subscriber' => $subscriber->getData(),
        ];

        if ($subscriber->getConfirmationLink()) {
            $data = [
                'confirmation_link' => $subscriber->getData(),
            ];
        }
        if ($subscriber->getCustomerId()) {
            try {
                $customer = $this->customerRegistry->retrieve($subscriber->getCustomerId());

                // Select needed data
                $data = [
                    'customer' => $this->getFullCustomerObject($customer)->getData(),
                ];
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
        $eventModel->setEventData($this->json->serialize($data));
        $this->eventRepository->save($eventModel);
    }

    /**
     * @param \Magento\Newsletter\Model\Subscriber $subscriber
     * @param callable $proceed
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function aroundSendConfirmationRequestEmail(
        \Magento\Newsletter\Model\Subscriber $subscriber,
        callable $proceed
    ) {
        if (!$this->configReader->isEnabled(ConfigInterface::MARKETING_EVENTS)) {
            return $proceed($subscriber);
        }

        $storeId = $subscriber->getStoreId();
        //add config later
        /*if (! $this->scopeConfig->getValue(
            path_in_the_config_table,
            'store',
            $storeId
        )
        ) {
            return $proceed();
        }*/
        /** @var \Emartech\Emarsys\Model\Event $eventModel */
        $eventModel = $this->eventFactory->create();
        $eventModel->setEventType(self::EVENT_NEWSLETTER_SEND_CONFIRMATION_REQUEST_EMAIL);

        $data = [
            'subscriber' => $subscriber->getData(),
        ];

        if ($subscriber->getConfirmationLink()) {
            $data = [
                'confirmation_link' => $subscriber->getData(),
            ];
        }
        if ($subscriber->getCustomerId()) {
            try {
                $customer = $this->customerRegistry->retrieve($subscriber->getCustomerId());

                // Select needed data
                $data = [
                    'customer' => $this->getFullCustomerObject($customer)->getData(),
                ];
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
        $eventModel->setEventData($this->json->serialize($data));
        $this->eventRepository->save($eventModel);
    }

    /**
     * @param \Magento\Newsletter\Model\Subscriber $subscriber
     * @param callable $proceed
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function aroundSendUnsubscriptionEmail(
        \Magento\Newsletter\Model\Subscriber $subscriber,
        callable $proceed
    ) {
        if (!$this->configReader->isEnabled(ConfigInterface::MARKETING_EVENTS)) {
            return $proceed($subscriber);
        }

        $storeId = $subscriber->getStoreId();
        //add config later
        /*if (! $this->scopeConfig->getValue(
            path_in_the_config_table,
            'store',
            $storeId
        )
        ) {
            return $proceed();
        }*/
        /** @var \Emartech\Emarsys\Model\Event $eventModel */
        $eventModel = $this->eventFactory->create();
        $eventModel->setEventType(self::EVENT_NEWSLETTER_SEND_UNSUBSCRIPTION_EMAIL);

        $data = [
            'subscriber' => $subscriber->getData(),
        ];

        if ($subscriber->getConfirmationLink()) {
            $data = [
                'confirmation_link' => $subscriber->getData(),
            ];
        }
        if ($subscriber->getCustomerId()) {
            try {
                $customer = $this->customerRegistry->retrieve($subscriber->getCustomerId());

                // Select needed data
                $data = [
                    'customer' => $this->getFullCustomerObject($customer)->getData(),
                ];
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
        $eventModel->setEventData($this->json->serialize($data));
        $this->eventRepository->save($eventModel);
    }

    /**
     * @param \Magento\Customer\Model\EmailNotificationInterface $emailNotification
     * @param callable $proceed
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param string $type
     * @param string $backUrl
     * @param string|int $storeId
     * @param string|null $sendemailStoreId
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundNewAccount(
        \Magento\Customer\Model\EmailNotificationInterface $emailNotification,
        callable $proceed,
        \Magento\Customer\Api\Data\CustomerInterface $customer,
        $type = \Magento\Customer\Model\EmailNotificationInterface::NEW_ACCOUNT_EMAIL_REGISTERED,
        $backUrl = '',
        $storeId = 0,
        $sendemailStoreId = null
    ) {
        if (!$this->configReader->isEnabled(ConfigInterface::MARKETING_EVENTS)) {
            return $proceed($customer, $type, $backUrl, $storeId, $sendemailStoreId);
        }

        if (!$storeId) {
            $storeId = $this->getWebsiteStoreId($customer, $sendemailStoreId);
        }

        $store = $this->storeManager->getStore($customer->getStoreId());

        /*if (! $this->scopeConfig->getValue(
            path_in_the_config_table,
            'store',
            $storeId
        )
        ) {
            return $proceed($customer, $type, $backUrl, $storeId, $sendemailStoreId);
        }*/

        /** @var \Emartech\Emarsys\Model\Event $eventModel */
        $eventModel = $this->eventFactory->create();
        $eventModel->setEventType(self::EVENT_CUSTOMER_NEW_ACCOUNT . $type);

        $data = [
            'customer' => $this->getFullCustomerObject($customer)->getData(),
            'back_url' => $backUrl,
            'store' => $store->getData(),
        ];

        $eventModel->setEventData($this->json->serialize($data));
        $this->eventRepository->save($eventModel);
    }


    /**
     * @param \Magento\Customer\Model\EmailNotificationInterface $emailNotification
     * @param callable $proceed
     * @param CustomerInterface $savedCustomer
     * @param $origCustomerEmail
     * @param bool $isPasswordChanged
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundCredentialsChanged(
        \Magento\Customer\Model\EmailNotificationInterface $emailNotification,
        callable $proceed,
        CustomerInterface $savedCustomer,
        $origCustomerEmail,
        $isPasswordChanged = false
    ) {
        if (!$this->configReader->isEnabled(ConfigInterface::MARKETING_EVENTS)) {
            return $proceed($savedCustomer, $origCustomerEmail, $isPasswordChanged);
        }

        $storeId = $storeId = $this->getWebsiteStoreId($savedCustomer);
        /*if (! $this->scopeConfig->getValue(
            path_in_the_config_table,
            'store',
            $storeId
        )
        ) {
            return $proceed($savedCustomer, $origCustomerEmail, $isPasswordChanged);
        }*/

        $store = $this->storeManager->getStore($storeId);
        if ($origCustomerEmail != $savedCustomer->getEmail()) {
            if ($isPasswordChanged) {
                /** @var \Emartech\Emarsys\Model\Event $eventModel */
                $eventModel = $this->eventFactory->create();
                $eventModel->setEventType(self::EVENT_CUSTOMER_EMAIL_AND_PASSWORD_CHANGED);
                $data = [
                    'customer' => $this->getFullCustomerObject($origCustomerEmail)->getData(),
                    'store' => $store->getData(),
                    'orig_customer_email' => $origCustomerEmail,
                    'new_customer_email' => $savedCustomer->getEmail()
                ];
                $eventModel->setEventData($this->json->serialize($data));
                $this->eventRepository->save($eventModel);
                return;
            }

            /** @var \Emartech\Emarsys\Model\Event $eventModel */
            $eventModel = $this->eventFactory->create();
            $eventModel->setEventType(self::EVENT_CUSTOMER_EMAIL_CHANGED);
            $data = [
                'customer' => $this->getFullCustomerObject($origCustomerEmail)->getData(),
                'store' => $store->getData(),
                'orig_customer_email' => $origCustomerEmail,
                'new_customer_email' => $savedCustomer->getEmail()
            ];
            $eventModel->setEventData($this->json->serialize($data));
            $this->eventRepository->save($eventModel);
            return;
        }

        if ($isPasswordChanged) {
            /** @var \Emartech\Emarsys\Model\Event $eventModel */
            $eventModel = $this->eventFactory->create();
            $eventModel->setEventType(self::EVENT_CUSTOMER_PASSWORD_RESET);
            $data = [
                'customer' => $this->getFullCustomerObject($origCustomerEmail)->getData(),
                'store' => $store->getData(),
                'orig_customer_email' => $origCustomerEmail,
                'new_customer_email' => $savedCustomer->getEmail()
            ];
            $eventModel->setEventData($this->json->serialize($data));
            $this->eventRepository->save($eventModel);
        }
    }

    /**
     * @param \Magento\Customer\Model\EmailNotificationInterface $emailNotification
     * @param callable $proceed
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundPasswordReminder(
        \Magento\Customer\Model\EmailNotificationInterface $emailNotification,
        callable $proceed,
        \Magento\Customer\Api\Data\CustomerInterface $customer
    ) {
        if (!$this->configReader->isEnabled(ConfigInterface::MARKETING_EVENTS)) {
            return $proceed($customer);
        }

        $storeId = $storeId = $this->getWebsiteStoreId($customer);
        /* if (! $this->scopeConfig->getValue(
            path_in_the_config_table,
            'store',
            $storeId
        )
        ) {
            return $proceed($customer);
        }*/
        $store = $this->storeManager->getStore($storeId);
        /** @var \Emartech\Emarsys\Model\Event $eventModel */
        $eventModel = $this->eventFactory->create();
        $eventModel->setEventType(self::EVENT_CUSTOMER_PASSWORD_REMINDER);

        $data = [
            'customer' => $this->getFullCustomerObject($customer)->getData(),
            $store->getData()
        ];

        $eventModel->setEventData($this->json->serialize($data));
        $this->eventRepository->save($eventModel);
    }

    /**
     * @param \Magento\Customer\Model\EmailNotificationInterface $emailNotification
     * @param callable $proceed
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundPasswordResetConfirmation(
        \Magento\Customer\Model\EmailNotificationInterface $emailNotification,
        callable $proceed,
        \Magento\Customer\Api\Data\CustomerInterface $customer
    ) {
        if (!$this->configReader->isEnabled(ConfigInterface::MARKETING_EVENTS)) {
            return $proceed($customer);
        }

        $storeId = $storeId = $this->getWebsiteStoreId($customer);
        /* if (! $this->scopeConfig->getValue(
            path_in_the_config_table,
            'store',
            $storeId
        )
        ) {
            return $proceed($customer);
        }*/
        $store = $this->storeManager->getStore($storeId);
        /** @var \Emartech\Emarsys\Model\Event $eventModel */
        $eventModel = $this->eventFactory->create();
        $eventModel->setEventType(self::EVENT_CUSTOMER_PASSWORD_RESET_CONFIRMATION);

        $data = [
            'customer' => $this->getFullCustomerObject($customer)->getData(),
            $store->getData()
        ];

        $eventModel->setEventData($this->json->serialize($data));
        $this->eventRepository->save($eventModel);
    }

    /**
     * Get either first store ID from a set website or the provided as default
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param int|string|null $defaultStoreId
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getWebsiteStoreId($customer, $defaultStoreId = null)
    {
        if ($customer->getWebsiteId() != 0 && empty($defaultStoreId)) {
            $storeIds = $this->storeManager->getWebsite($customer->getWebsiteId())->getStoreIds();
            $defaultStoreId = reset($storeIds);
        }
        return $defaultStoreId;
    }

    /**
     * Create an object with data merged from Customer and CustomerSecure
     *
     * @param CustomerInterface $customer
     * @return \Magento\Customer\Model\Data\CustomerSecure
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getFullCustomerObject($customer)
    {
        // No need to flatten the custom attributes or nested objects since the only usage is for email templates and
        // object passed for events
        $mergedCustomerData = $this->customerRegistry->retrieveSecureData($customer->getId());
        $customerData = $this->dataProcessor
            ->buildOutputDataArray($customer, \Magento\Customer\Api\Data\CustomerInterface::class);
        $mergedCustomerData->addData($customerData);
        $mergedCustomerData->setData('name', $this->customerViewHelper->getCustomerName($customer));
        return $mergedCustomerData;
    }

}

<?php

namespace Emartech\Emarsys\Events;

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
     * CustomerPlugin constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     * @param EventRepository $eventRepository
     * @param CustomerRegistry $customerRegistry
     * @param EmarsysEventFactory $eventFactory
     * @param DataObjectProcessor $dataProcessor
     * @param CustomerViewHelper $customerViewHelper
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
        $eventModel->setEventType('newsletter_send_confirmation_success_email');

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
                $customer = $this->getFullCustomerObject($this->customerRegistry->retrieve($subscriber->getCustomerId()));

                // Select needed data
                $data = [
                    'customer' => $customer->getData(),
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
        $eventModel->setEventType('newsletter_send_confirmation_request_email');

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
                $customer = $this->getFullCustomerObject($this->customerRegistry->retrieve($subscriber->getCustomerId()));

                // Select needed data
                $data = [
                    'customer' => $customer->getData(),
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
        $eventModel->setEventType('newsletter_send_unsubscription_email');

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
                $customer = $this->getFullCustomerObject($this->customerRegistry->retrieve($subscriber->getCustomerId()));

                // Select needed data
                $data = [
                    'customer' => $customer->getData(),
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
        $eventModel->setEventType('customer_new_account_'. $type);

        $data = [
            'customer' => $this->getFullCustomerObject($customer),
            'back_url' => $backUrl,
            'store' => $store->getData(),
        ];

        $eventModel->setEventData($this->json->serialize($data));
        $this->eventRepository->save($eventModel);
    }

    /**
     * @param \Magento\Customer\Model\EmailNotificationInterface $emailNotification
     * @param callable $proceed
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param string $email
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundEmailAndPasswordChanged(
        \Magento\Customer\Model\EmailNotificationInterface $emailNotification,
        callable $proceed,
        \Magento\Customer\Api\Data\CustomerInterface $customer,
        $email
    ) {
        $storeId = $storeId = $this->getWebsiteStoreId($customer);
        /*if (! $this->scopeConfig->getValue(
            path_in_the_config_table,
            'store',
            $storeId
        )
        ) {
            return $proceed($customer, $email);
        }*/

        $store = $this->storeManager->getStore($storeId);
        /** @var \Emartech\Emarsys\Model\Event $eventModel */
        $eventModel = $this->eventFactory->create();
        $eventModel->setEventType('customer_email_and_password_changed');

        $data = [
            'customer' => $this->getFullCustomerObject($customer),
            'store' => $store->getData()
        ];

        $eventModel->setEventData($this->json->serialize($data));
        $this->eventRepository->save($eventModel);
    }

    /**
     * @param \Magento\Customer\Model\EmailNotificationInterface $emailNotification
     * @param callable $proceed
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param string $email
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundEmailChanged(
        \Magento\Customer\Model\EmailNotificationInterface $emailNotification,
        callable $proceed,
        \Magento\Customer\Api\Data\CustomerInterface $customer,
        $email
    ) {
        $storeId = $storeId = $this->getWebsiteStoreId($customer);
        /*if (! $this->scopeConfig->getValue(
            path_in_the_config_table,
            'store',
            $storeId
        )
        ) {
            return $proceed($customer, $email);
        }*/
        $store = $this->storeManager->getStore($storeId);
        /** @var \Emartech\Emarsys\Model\Event $eventModel */
        $eventModel = $this->eventFactory->create();
        $eventModel->setEventType('customer_email_changed');

        $data = [
            'customer' => $this->getFullCustomerObject($customer),
            'store' => $store->getId()
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
    public function aroundPasswordReset(
        \Magento\Customer\Model\EmailNotificationInterface $emailNotification,
        callable $proceed,
        \Magento\Customer\Api\Data\CustomerInterface $customer
    ) {
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
        $eventModel->setEventType('customer_password_reset');

        $data = [
            'customer' => $this->getFullCustomerObject($customer),
            $store->getId()
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
    public function aroundPasswordReminder(
        \Magento\Customer\Model\EmailNotificationInterface $emailNotification,
        callable $proceed,
        \Magento\Customer\Api\Data\CustomerInterface $customer
    ) {
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
        $eventModel->setEventType('customer_password_reminder');

        $data = [
            'customer' => $this->getFullCustomerObject($customer),
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
        $eventModel->setEventType('customer_password_reset_confirmation');

        $data = [
            'customer' => $this->getFullCustomerObject($customer),
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
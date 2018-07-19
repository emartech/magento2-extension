<?php

namespace Emartech\Emarsys\Events;

use Emartech\Emarsys\Model\EventRepository;
use Emartech\Emarsys\Model\SettingsFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\StoreManagerInterface;
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
     * @param Json $json
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger,
        EventRepository $eventRepository,
        CustomerRegistry $customerRegistry,
        EmarsysEventFactory $eventFactory,
        Json $json

    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->eventRepository = $eventRepository;
        $this->customerRegistry = $customerRegistry;
        $this->eventFactory = $eventFactory;
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
                $customer = $this->customerRegistry->retrieve($subscriber->getCustomerId());

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
                $customer = $this->customerRegistry->retrieve($subscriber->getCustomerId());

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
                $customer = $this->customerRegistry->retrieve($subscriber->getCustomerId());

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
     * @param \Magento\Customer\Model\Customer $customer
     * @param callable $proceed
     * @param string $type
     * @param string $backUrl
     * @param string $storeId
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function aroundSendNewAccountEmail(
        \Magento\Customer\Model\Customer $customer,
        callable $proceed,
        $type = 'registered',
        $backUrl = '',
        $storeId = '0'
    ) {
        exit('2');
        $storeId = $customer->getStoreId();

        $template = 'new_account_email';
        $emailData = [];

        /** @var EventRepository $eventModel */
        $eventModel = $this->eventRepository->save();

        $eventModel->setEventType($template);
        $eventModel->setEventData($emailData);
        //$this->eventResource->save($eventModel);

        $this->logger->info('event_type: '. $template . ', event_data: '.json_encode($emailData));

        //add config later
        /*if (! $this->scopeConfig->getValue(
            path_in_the_config_table,
            'store',
            $storeId
        )
        ) {
            return $proceed($type, $backUrl, $storeId);
        }*/
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
        exit('3');
        if (!$storeId) {
            $storeId = $this->getWebsiteStoreId($customer, $sendemailStoreId);
        }


        /*if (! $this->scopeConfig->getValue(
            path_in_the_config_table,
            'store',
            $storeId
        )
        ) {
            return $proceed($customer, $type, $backUrl, $storeId, $sendemailStoreId);
        }*/
    }

    /**
     * Get either first store ID from a set website or the provided as default
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param int|string|null $defaultStoreId
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getWebsiteStoreId($customer, $defaultStoreId = null)
    {
        if ($customer->getWebsiteId() != 0 && empty($defaultStoreId)) {
            $storeIds = $this->storeManager->getWebsite($customer->getWebsiteId())->getStoreIds();
            $defaultStoreId = reset($storeIds);
        }
        return $defaultStoreId;
    }
}

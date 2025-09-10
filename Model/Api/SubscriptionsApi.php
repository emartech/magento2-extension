<?php

namespace Emartech\Emarsys\Model\Api;

use Emartech\Emarsys\Api\Data\ErrorResponseItemInterface;
use Emartech\Emarsys\Api\Data\ErrorResponseItemInterfaceFactory;
use Emartech\Emarsys\Api\Data\StatusResponseInterface;
use Emartech\Emarsys\Api\Data\StatusResponseInterfaceFactory;
use Emartech\Emarsys\Api\Data\SubscriptionInterface;
use Emartech\Emarsys\Api\Data\SubscriptionInterfaceFactory;
use Emartech\Emarsys\Api\Data\SubscriptionsApiResponseInterface;
use Emartech\Emarsys\Api\Data\SubscriptionsApiResponseInterfaceFactory;
use Emartech\Emarsys\Api\SubscriptionsApiInterface;
use Exception;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Config\Share;
use Magento\Framework\DataObject;
use Magento\Newsletter\Model\ResourceModel\Subscriber\Collection;
use Magento\Newsletter\Model\ResourceModel\Subscriber\CollectionFactory;
use Magento\Newsletter\Model\Subscriber;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Store\Model\StoreManagerInterface;

class SubscriptionsApi implements SubscriptionsApiInterface
{
    /**
     * @var CollectionFactory
     */
    private $subscriberCollectionFactory;

    /**
     * @var SubscriberFactory
     */
    private $subscriberFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Share
     */
    private $customerModelConfigShare;

    /**
     * @var StatusResponseInterfaceFactory
     */
    private $statusResponseFactory;

    /**
     * @var ErrorResponseItemInterfaceFactory
     */
    private $errorResponseItemFactory;

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @var SubscriptionInterfaceFactory
     */
    private $subscriptionFactory;

    /**
     * @var SubscriptionsApiResponseInterfaceFactory
     */
    private $subscriptionsResponseFactory;

    /**
     * @var Collection
     */
    private $subscriptionCollection;

    /**
     * @var string
     */
    private $storeTableName;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * SubscriptionsApi constructor.
     *
     * @param CollectionFactory                        $subscriberCollectionFactory
     * @param SubscriberFactory                        $subscriberFactory
     * @param StoreManagerInterface                    $storeManager
     * @param SubscriptionInterfaceFactory             $subscriptionFactory
     * @param Share                                    $customerModelConfigShare
     * @param SubscriptionsApiResponseInterfaceFactory $subscriptionsResponseFactory
     * @param StatusResponseInterfaceFactory           $statusResponseFactory
     * @param ErrorResponseItemInterfaceFactory        $errorResponseItemFactory
     * @param CustomerRepositoryInterface              $customerRepository
     */
    public function __construct(
        CollectionFactory $subscriberCollectionFactory,
        SubscriberFactory $subscriberFactory,
        StoreManagerInterface $storeManager,
        SubscriptionInterfaceFactory $subscriptionFactory,
        Share $customerModelConfigShare,
        SubscriptionsApiResponseInterfaceFactory $subscriptionsResponseFactory,
        StatusResponseInterfaceFactory $statusResponseFactory,
        ErrorResponseItemInterfaceFactory $errorResponseItemFactory,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->storeManager = $storeManager;
        $this->customerModelConfigShare = $customerModelConfigShare;

        $this->subscriberFactory = $subscriberFactory;
        $this->statusResponseFactory = $statusResponseFactory;
        $this->errorResponseItemFactory = $errorResponseItemFactory;

        $this->subscriberCollectionFactory = $subscriberCollectionFactory;
        $this->subscriptionFactory = $subscriptionFactory;
        $this->subscriptionsResponseFactory = $subscriptionsResponseFactory;
        $this->customerRepository = $customerRepository;
    }

    /**
     * Get
     *
     * @param int         $page
     * @param int         $pageSize
     * @param bool|null   $subscribed
     * @param bool        $onlyGuest
     * @param string|null $websiteId
     * @param string|null $storeId
     *
     * @return SubscriptionsApiResponseInterface
     */
    public function get(
        int $page = 1,
        int $pageSize = 1000,
        ?bool $subscribed = null,
        bool $onlyGuest = false,
        ?string $websiteId = null,
        ?string $storeId = null
    ): SubscriptionsApiResponseInterface {

        $this
            ->initCollection()
            ->joinWebsite()
            ->filterWebsite($websiteId)
            ->filterStore($storeId)
            ->filterSubscribed($subscribed)
            ->filterCustomers($onlyGuest)
            ->setPage($page, $pageSize);

        return $this->subscriptionsResponseFactory
            ->create()
            ->setCurrentPage($this->subscriptionCollection->getCurPage())
            ->setLastPage($this->subscriptionCollection->getLastPageNumber())
            ->setPageSize($this->subscriptionCollection->getPageSize())
            ->setTotalCount($this->subscriptionCollection->getSize())
            ->setSubscriptions($this->handleSubscriptions());
    }

    /**
     * Update
     *
     * @param array $subscriptions
     *
     * @return StatusResponseInterface
     */
    public function update(array $subscriptions): StatusResponseInterface
    {
        /** @var SubscriptionInterface $subscription */
        foreach ($subscriptions as $subscription) {
            try {
                $this->changeSubscription(
                    $subscription,
                    (bool) $subscription->getSubscriberStatus() === true ?
                        Subscriber::STATUS_SUBSCRIBED :
                        Subscriber::STATUS_UNSUBSCRIBED
                );
            } catch (Exception $e) {
                $this->addError($subscription, $e);
            }
        }

        return $this->handleResponse();
    }

    /**
     * HandleResponse
     *
     * @return StatusResponseInterface
     */
    private function handleResponse(): StatusResponseInterface
    {
        $status = 'ok';
        $errors = null;
        if (count($this->errors)) {
            $status = 'error';
            $errors = $this->getErrors();
        }

        return $this->statusResponseFactory
            ->create()
            ->setErrors($errors)
            ->setStatus($status);
    }

    /**
     * AddError
     *
     * @param SubscriptionInterface $subscription
     * @param Exception             $error
     *
     * @return SubscriptionsApi
     */
    private function addError(SubscriptionInterface $subscription, Exception $error): SubscriptionsApi
    {
        $this->errors[] = [
            'email'       => $subscription->getSubscriberEmail(),
            'customer_id' => $subscription->getCustomerId(),
            'message'     => $error->getMessage(),
        ];

        return $this;
    }

    /**
     * GetErrors
     *
     * @return ErrorResponseItemInterface[]
     */
    private function getErrors(): array
    {
        $returnArray = [];
        foreach ($this->errors as $error) {
            $returnArray[] = $this->errorResponseItemFactory
                ->create()
                ->setEmail($error['email'])
                ->setCustomerId($error['customer_id'])
                ->setMessage($error['message']);
        }

        return $returnArray;
    }

    /**
     * InitCollection
     *
     * @return SubscriptionsApi
     */
    private function initCollection(): SubscriptionsApi
    {
        $this->subscriptionCollection = $this->subscriberCollectionFactory->create();
        $this->storeTableName = $this->subscriptionCollection->getResource()->getTable('store');

        return $this;
    }

    /**
     * HandleSubscriptions
     *
     * @return array
     */
    private function handleSubscriptions(): array
    {
        $subscriptionArray = [];
        foreach ($this->subscriptionCollection as $subscription) {
            $subscriptionArray[] = $this->parseSubscription($subscription);
        }

        return $subscriptionArray;
    }

    /**
     * ParseSubscription
     *
     * @param DataObject $subscription
     *
     * @return SubscriptionInterface
     */
    private function parseSubscription(DataObject $subscription): SubscriptionInterface
    {
        $subscriptionItem = $this->subscriptionFactory->create();

        foreach ($subscription->getData() as $key => $value) {
            $subscriptionItem->setData($key, $value);
        }

        return $subscriptionItem;
    }

    /**
     * SetPage
     *
     * @param int $page
     * @param int $pageSize
     *
     * @return SubscriptionsApi
     */
    private function setPage(int $page, int $pageSize): SubscriptionsApi
    {
        $this->subscriptionCollection
            ->setCurPage($page)
            ->setPageSize($pageSize);

        return $this;
    }

    /**
     * JoinWebsite
     *
     * @return SubscriptionsApi
     */
    private function joinWebsite(): SubscriptionsApi
    {
        $this->subscriptionCollection->getSelect()->joinLeft(
            [$this->storeTableName],
            $this->storeTableName . '.store_id = main_table.store_id',
            ['website_id']
        );

        return $this;
    }

    /**
     * FilterWebsite
     *
     * @param string|null $websiteId
     *
     * @return SubscriptionsApi
     */
    private function filterWebsite(?string $websiteId = null): SubscriptionsApi
    {
        if ($websiteId !== null) {
            if (!is_array($websiteId)) {
                $websiteId = explode(',', $websiteId);
            }
            $this->subscriptionCollection->addFieldToFilter(
                $this->storeTableName . '.website_id',
                ['in' => $websiteId]
            );
        }

        return $this;
    }

    /**
     * FilterStore
     *
     * @param string|null $storeId
     *
     * @return SubscriptionsApi
     */
    private function filterStore(?string $storeId = null): SubscriptionsApi
    {
        if ($storeId !== null) {
            if (!is_array($storeId)) {
                $storeId = explode(',', $storeId);
            }
            $this->subscriptionCollection->addFieldToFilter('store_id', ['in' => $storeId]);
        }

        return $this;
    }

    /**
     * FilterSubscribed
     *
     * @param bool $subscribed
     *
     * @return SubscriptionsApi
     */
    private function filterSubscribed(?bool $subscribed = null): SubscriptionsApi
    {
        if ($subscribed === true) {
            $this->subscriptionCollection->addFieldToFilter('subscriber_status', ['eq' => 1]);
        } elseif ($subscribed === false) {
            $this->subscriptionCollection->addFieldToFilter('subscriber_status', ['neq' => 1]);
        }

        return $this;
    }

    /**
     * FilterCustomers
     *
     * @param bool|null $onlyGuest
     *
     * @return SubscriptionsApi
     */
    private function filterCustomers(?bool $onlyGuest = null): SubscriptionsApi
    {
        if ($onlyGuest) {
            $this->subscriptionCollection->addFieldToFilter('customer_id', ['eq' => 0]);
        }

        return $this;
    }

    /**
     * FilterCustomer
     *
     * @param int $customerId
     *
     * @return SubscriptionsApi
     */
    private function filterCustomer(int $customerId): SubscriptionsApi
    {
        $this->subscriptionCollection->addFieldToFilter('customer_id', ['eq' => (int) $customerId]);

        return $this;
    }

    /**
     * FilterEmail
     *
     * @param string $email
     *
     * @return SubscriptionsApi
     */
    private function filterEmail(string $email): SubscriptionsApi
    {
        $this->subscriptionCollection->addFieldToFilter('subscriber_email', ['eq' => $email]);

        return $this;
    }

    /**
     * ChangeSubscription
     *
     * @param SubscriptionInterface $subscription
     * @param int                   $type
     *
     * @return bool
     * @throws Exception
     */
    private function changeSubscription(SubscriptionInterface $subscription, int $type): bool
    {
        if ($subscription->getSubscriberEmail()) {
            $this
                ->initCollection()
                ->filterEmail($subscription->getSubscriberEmail());
            if ($subscription->getCustomerId()) {
                $this->filterCustomer($subscription->getCustomerId());
            }

            if ($this->customerModelConfigShare->isWebsiteScope()) {
                $this
                    ->joinWebsite()
                    ->filterWebsite($subscription->getWebsiteId());
            }

            /** @var Subscriber $subscriber |bool */
            $subscriber = $this->subscriptionCollection->fetchItem();

            if (!$subscriber) {
                if ($type !== Subscriber::STATUS_SUBSCRIBED
                    || !$subscription->getCustomerId()
                    || null === ($customer = $this->getCustomerData($subscription->getCustomerId()))
                    || $customer->getWebsiteId() != $subscription->getWebsiteId()
                    || $customer->getEmail() != $subscription->getSubscriberEmail()
                ) {
                    return false;
                }

                $subscriber = $this->subscriberFactory->create();
                $subscription->setStoreId($customer->getStoreId());
            }

            foreach ($subscription->getData() as $key => $value) {
                $subscriber->setData($key, $value);
            }

            $subscriber->setStatus($type);
            $subscriber->setStatusChanged(true);

            $subscriber->save();

            return true;
        }

        return false;
    }

    /**
     * GetCustomerData
     *
     * @param int|null $customerId
     *
     * @return CustomerInterface|null
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getCustomerData(?int $customerId = null): ?CustomerInterface
    {
        return $this->customerRepository->getById($customerId);
    }
}

<?php

namespace Emartech\Emarsys\Model\Api;

use Emartech\Emarsys\Api\Data\ErrorResponseItemInterface;
use Emartech\Emarsys\Api\Data\ErrorResponseItemInterfaceFactory;
use Emartech\Emarsys\Api\Data\StatusResponseInterface;
use Emartech\Emarsys\Api\Data\StatusResponseInterfaceFactory;
use Emartech\Emarsys\Api\Data\SubscriptionInterface;
use Emartech\Emarsys\Api\Data\SubscriptionInterfaceFactory;
use Emartech\Emarsys\Api\Data\SubscriptionsApiResponseInterfaceFactory;
use Emartech\Emarsys\Api\SubscriptionsApiInterface;
use Exception;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Config\Share;
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
     * {@inheritdoc}
     */
    public function get(
        $page = 1,
        $pageSize = 1000,
        $subscribed = null,
        $onlyGuest = false,
        $websiteId = null,
        $storeId = null
    ) {

        $this
            ->initCollection()
            ->joinWebsite()
            ->filterWebsite($websiteId)
            ->filterStore($storeId)
            ->filterSubscribed($subscribed)
            ->filterCustomers($onlyGuest)
            ->setPage($page, $pageSize);

        return $this->subscriptionsResponseFactory->create()
            ->setCurrentPage($this->subscriptionCollection->getCurPage())
            ->setLastPage($this->subscriptionCollection->getLastPageNumber())
            ->setPageSize($this->subscriptionCollection->getPageSize())
            ->setTotalCount($this->subscriptionCollection->getSize())
            ->setSubscriptions($this->handleSubscriptions());
    }

    /**
     * {@inheritdoc}
     */
    public function update($subscriptions)
    {
        /** @var SubscriptionInterface $subscription */
        foreach ($subscriptions as $subscription) {
            try {
                $this->changeSubscription(
                    $subscription,
                    (bool)$subscription->getSubscriberStatus() === true ?
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
     * @return StatusResponseInterface
     */
    private function handleResponse()
    {
        $status = 'ok';
        $errors = null;
        if (count($this->errors)) {
            $status = 'error';
            $errors = $this->getErrors();
        }

        return $this->statusResponseFactory->create()
            ->setErrors($errors)
            ->setStatus($status);
    }

    /**
     * @param SubscriptionInterface $subscription
     * @param Exception             $error
     *
     * @return $this
     */
    private function addError($subscription, $error)
    {
        $this->errors[] = [
            'email'       => $subscription->getSubscriberEmail(),
            'customer_id' => $subscription->getCustomerId(),
            'message'     => $error->getMessage(),
        ];

        return $this;
    }

    /**
     * @return ErrorResponseItemInterface[]
     */
    private function getErrors()
    {
        $returnArray = [];
        foreach ($this->errors as $error) {
            $returnArray[] = $this->errorResponseItemFactory->create()
                ->setEmail($error['email'])
                ->setCustomerId($error['customer_id'])
                ->setMessage($error['message']);
        }

        return $returnArray;
    }

    /**
     * @return $this
     */
    private function initCollection()
    {
        $this->subscriptionCollection = $this->subscriberCollectionFactory->create();
        $this->storeTableName = $this->subscriptionCollection->getResource()->getTable('store');

        return $this;
    }

    /**
     * @return array
     */
    private function handleSubscriptions()
    {
        $subscriptionArray = [];
        foreach ($this->subscriptionCollection as $subscription) {
            $subscriptionArray[] = $this->parseSubscription($subscription);
        }

        return $subscriptionArray;
    }

    /**
     * @param $subscription
     *
     * @return SubscriptionInterface
     */
    private function parseSubscription($subscription)
    {
        /** @var SubscriptionInterface $subscriptionItem */
        $subscriptionItem = $this->subscriptionFactory->create();

        foreach ($subscription->getData() as $key => $value) {
            $subscriptionItem->setData($key, $value);
        }

        return $subscriptionItem;
    }

    /**
     * @param int $page
     * @param int $pageSize
     *
     * @return $this
     */
    private function setPage($page, $pageSize)
    {
        $this->subscriptionCollection
            ->setCurPage($page)
            ->setPageSize($pageSize);
        return $this;
    }

    /**
     * @return $this
     */
    private function joinWebsite()
    {

        // @codingStandardsIgnoreLine
        $this->subscriptionCollection->getSelect()->joinLeft(
            [$this->storeTableName],
            $this->storeTableName . '.store_id = main_table.store_id',
            ['website_id']
        );

        return $this;
    }

    /**
     * @param int|string|null $websiteId
     *
     * @return $this
     */
    private function filterWebsite($websiteId = null)
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
     * @param int|string|null $storeId
     *
     * @return $this
     */
    private function filterStore($storeId = null)
    {
        if ($storeId !== null) {
            if (!is_array($storeId)) {
                $storeId = explode(',', $storeId);
            }
            $this->subscriptionCollection->addFieldToFilter('store_id',
                ['in' => $storeId]);
        }

        return $this;
    }

    /**
     * @param bool $subscribed
     *
     * @return $this
     */
    private function filterSubscribed($subscribed = null)
    {
        if ($subscribed === true) {
            $this->subscriptionCollection->addFieldToFilter('subscriber_status',
                ['eq' => 1]);
        } elseif ($subscribed === false) {
            $this->subscriptionCollection->addFieldToFilter('subscriber_status',
                ['neq' => 1]);
        }
        return $this;
    }

    /**
     * @param mixed $onlyGuest
     *
     * @return $this
     */
    private function filterCustomers($onlyGuest = null)
    {
        if ((bool)$onlyGuest) {
            $this->subscriptionCollection->addFieldToFilter('customer_id',
                ['eq' => 0]);
        }
        return $this;
    }

    /**
     * @param int $customerId
     *
     * @return $this
     */
    private function filterCustomer($customerId)
    {
        $this->subscriptionCollection->addFieldToFilter('customer_id',
            ['eq' => (int)$customerId]);

        return $this;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    private function filterEmail($email)
    {
        $this->subscriptionCollection->addFieldToFilter('subscriber_email',
            ['eq' => $email]);

        return $this;
    }

    /**
     * @param SubscriptionInterface $subscription
     * @param string                $type
     *
     * @return bool
     * @throws Exception
     */
    private function changeSubscription($subscription, $type)
    {
        if ($subscription->getSubscriberEmail()) {
            $this
                ->initCollection()
                ->filterEmail($subscription->getSubscriberEmail())
                ->filterCustomer($subscription->getCustomerId());

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
                    || false === ($customer = $this->getCustomerData($subscription->getCustomerId()))
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
     * @param int $customerId
     *
     * @return bool|CustomerInterface
     *
     * @throws Exception
     */
    private function getCustomerData($customerId)
    {
        return $this->customerRepository->getById($customerId);
    }
}

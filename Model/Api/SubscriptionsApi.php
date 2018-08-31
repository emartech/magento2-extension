<?php

namespace Emartech\Emarsys\Model\Api;

use Magento\Newsletter\Model\ResourceModel\Subscriber\CollectionFactory;
use Magento\Newsletter\Model\ResourceModel\Subscriber\Collection;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Newsletter\Model\Subscriber;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Config\Share;

use Emartech\Emarsys\Api\SubscriptionsApiInterface;
use Emartech\Emarsys\Api\Data\StatusResponseInterfaceFactory;
use Emartech\Emarsys\Api\Data\StatusResponseInterface;
use Emartech\Emarsys\Api\Data\SubscriptionInterfaceFactory;
use Emartech\Emarsys\Api\Data\SubscriptionInterface;
use Emartech\Emarsys\Api\Data\SubscriptionsApiResponseInterfaceFactory;
use Emartech\Emarsys\Api\Data\SubscriptionsApiResponseInterface;

/**
 * Class SubscriptionsApi
 * @package Emartech\Emarsys\Model\Api
 */
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
     * SubscriptionsApi constructor.
     *
     * @param CollectionFactory                        $subscriberCollectionFactory
     * @param SubscriberFactory                        $subscriberFactory
     * @param StoreManagerInterface                    $storeManager
     * @param SubscriptionInterfaceFactory             $subscriptionFactory
     * @param Share                                    $customerModelConfigShare
     * @param SubscriptionsApiResponseInterfaceFactory $subscriptionsResponseFactory
     * @param StatusResponseInterfaceFactory           $statusResponseFactory
     */
    public function __construct(
        CollectionFactory $subscriberCollectionFactory,
        SubscriberFactory $subscriberFactory,
        StoreManagerInterface $storeManager,
        SubscriptionInterfaceFactory $subscriptionFactory,
        Share $customerModelConfigShare,
        SubscriptionsApiResponseInterfaceFactory $subscriptionsResponseFactory,
        StatusResponseInterfaceFactory $statusResponseFactory
    ) {
        $this->storeManager = $storeManager;
        $this->customerModelConfigShare = $customerModelConfigShare;

        $this->subscriberFactory = $subscriberFactory;
        $this->statusResponseFactory = $statusResponseFactory;

        $this->subscriberCollectionFactory = $subscriberCollectionFactory;
        $this->subscriptionFactory = $subscriptionFactory;
        $this->subscriptionsResponseFactory = $subscriptionsResponseFactory;
    }

    /**
     * @param int   $page
     * @param int   $pageSize
     * @param bool  $subscribed
     * @param bool  $onlyGuest
     * @param mixed $websiteId
     * @param mixed $storeId
     *
     * @return SubscriptionsApiResponseInterface
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
     * @param SubscriptionInterface[] $subscriptions
     *
     * @return StatusResponseInterface
     * @throws \Exception
     */
    public function update($subscriptions)
    {
        $this->initCollection();

        /** @var SubscriptionInterface $subscription */
        foreach ($subscriptions as $subscription) {
            $this->changeSubscription(
                $subscription,
                (bool)$subscription->getSubscriberStatus() === true ?
                    Subscriber::STATUS_SUBSCRIBED :
                    Subscriber::STATUS_UNSUBSCRIBED
            );
        }

        return $this->statusResponseFactory->create()
            ->setStatus('ok');
    }

    /**
     * @return $this
     */
    private function initCollection()
    {
        $this->subscriptionCollection = $this->subscriberCollectionFactory->create();

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
        $storeTable = $this->subscriptionCollection->getResource()->getTable('store');

        $this->subscriptionCollection->getSelect()->joinLeft(
            [$storeTable],
            $storeTable . '.store_id = main_table.store_id',
            ['website_id']
        );

        return $this;
    }

    /**
     * @param int|null $websiteId
     *
     * @return $this
     */
    private function filterWebsite($websiteId = null)
    {
        if ($websiteId) {
            if (!is_array($websiteId)) {
                $websiteId = explode(',', $websiteId);
            }
            $this->subscriptionCollection->addFieldToFilter('website_id', ['in' => $websiteId]);
        }

        return $this;
    }

    /**
     * @param mixed $storeId
     *
     * @return $this
     */
    private function filterStore($storeId = null)
    {
        if ($storeId) {
            if (!is_array($storeId)) {
                $storeId = explode(',', $storeId);
            }
            $this->subscriptionCollection->addFieldToFilter('store_id', ['in' => $storeId]);
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
            $this->subscriptionCollection->addFieldToFilter('subscriber_status', ['eq' => 1]);
        } elseif ($subscribed === false) {
            $this->subscriptionCollection->addFieldToFilter('subscriber_status', ['neq' => 1]);
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
            $this->subscriptionCollection->addFieldToFilter('customer_id', ['eq' => 0]);
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
        $this->subscriptionCollection->addFieldToFilter('customer_id', ['eq' => (int)$customerId]);

        return $this;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    private function filterEmail($email)
    {
        $this->subscriptionCollection->addFieldToFilter('subscriber_email', ['eq' => $email]);

        return $this;
    }

    /**
     * @param SubscriptionInterface $subscription
     * @param string                $type
     *
     * @return bool
     * @throws \Exception
     */
    private function changeSubscription($subscription, $type)
    {
        if ($subscription->getSubscriberEmail()) {
            $this
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
                if ($type !== Subscriber::STATUS_SUBSCRIBED) {
                    return false;
                }

                $subscriber = $this->subscriberFactory->create();
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
}

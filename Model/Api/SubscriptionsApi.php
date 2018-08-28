<?php

namespace Emartech\Emarsys\Model\Api;

use Magento\Newsletter\Model\Subscriber;

/**
 * Class SubscriptionsApi
 * @package Emartech\Emarsys\Model\Api
 */
class SubscriptionsApi implements \Emartech\Emarsys\Api\SubscriptionsApiInterface
{
    /**
     * @var \Magento\Newsletter\Model\ResourceModel\Subscriber\CollectionFactory
     */
    protected $subscriberCollectionFactory;

    /**
     * @var \Magento\Newsletter\Model\SubscriberFactory
     */
    protected $subscriberFactory;

    /**
     * @var \Emartech\Emarsys\Api\Data\StatusResponseInterfaceFactory
     */
    protected $statusResponseFactory;

    /**
     * @var \Emartech\Emarsys\Api\Data\SubscriptionInterfaceFactory
     */
    protected $subscriptionFactory;

    /**
     * @var \Emartech\Emarsys\Api\Data\SubscriptionsApiResponseInterfaceFactory
     */
    protected $subscriptionsResponseFactory;

    /**
     * @var \Magento\Newsletter\Model\ResourceModel\Subscriber\Collection
     */
    protected $subscriptionCollection;

    /**
     * Subscription constructor.
     *
     * @param \Magento\Newsletter\Model\ResourceModel\Subscriber\CollectionFactory $subscriberCollectionFactory
     * @param \Magento\Newsletter\Model\SubscriberFactory                          $subscriberFactory
     * @param \Emartech\Emarsys\Api\Data\SubscriptionInterfaceFactory              $subscriptionFactory
     * @param \Emartech\Emarsys\Api\Data\SubscriptionsApiResponseInterfaceFactory  $subscriptionsResponseFactory
     * @param \Emartech\Emarsys\Api\Data\StatusResponseInterfaceFactory            $statusResponseFactory
     */
    public function __construct(
        \Magento\Newsletter\Model\ResourceModel\Subscriber\CollectionFactory $subscriberCollectionFactory,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        \Emartech\Emarsys\Api\Data\SubscriptionInterfaceFactory $subscriptionFactory,
        \Emartech\Emarsys\Api\Data\SubscriptionsApiResponseInterfaceFactory $subscriptionsResponseFactory,
        \Emartech\Emarsys\Api\Data\StatusResponseInterfaceFactory $statusResponseFactory
    ) {
        $this->subscriberFactory = $subscriberFactory;
        $this->statusResponseFactory = $statusResponseFactory;

        $this->subscriberCollectionFactory = $subscriberCollectionFactory;
        $this->subscriptionFactory = $subscriptionFactory;
        $this->subscriptionsResponseFactory = $subscriptionsResponseFactory;

        $this->subscriptionCollection = $this->subscriberCollectionFactory->create();
    }

    /**
     * @param int   $page
     * @param int   $pageSize
     * @param mixed $subscribed
     * @param mixed $onlyGuest
     * @param mixed $websiteId
     * @param mixed $storeId
     *
     * @return \Emartech\Emarsys\Api\Data\SubscriptionsApiResponseInterface
     */
    public function get($page = 1, $pageSize = 1000, $subscribed = null, $onlyGuest = null, $websiteId = null, $storeId = null)
    {
        $this
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
     * @param \Emartech\Emarsys\Api\Data\SubscriptionInterface[] $subscriptions
     *
     * @return \Emartech\Emarsys\Api\Data\StatusResponseInterface
     * @throws \Exception
     */
    public function update($subscriptions)
    {
        /** @var \Emartech\Emarsys\Api\Data\SubscriptionInterface $subscription */
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

    protected function handleSubscriptions()
    {
        $subscriptionArray = [];
        foreach ($this->subscriptionCollection as $subscription) {
            $subscriptionArray[] = $this->parseSubscription($subscription);
        }

        return $subscriptionArray;
    }

    /**
     * @param \Magento\Newsletter\Model\Subscriber $subscription
     *
     * @return \Emartech\Emarsys\Api\Data\SubscriptionInterface
     */
    protected function parseSubscription($subscription)
    {
        /** @var \Emartech\Emarsys\Api\Data\SubscriptionInterface $subscriptionItem */
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
    protected function setPage($page, $pageSize)
    {
        $this->subscriptionCollection
            ->setCurPage($page)
            ->setPageSize($pageSize);
        return $this;
    }

    /**
     * @return $this
     */
    protected function joinWebsite()
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
     * @param mixed $websiteId
     *
     * @return $this
     */
    protected function filterWebsite($websiteId = null)
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
    protected function filterStore($storeId = null)
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
     * @param mixed $subscribed
     *
     * @return $this
     */
    protected function filterSubscribed($subscribed = null)
    {
        if ($subscribed !== null) {
            $this->subscriptionCollection->addFieldToFilter('subscriber_status', ['eq' => (int)$subscribed]);
        }
        return $this;
    }

    /**
     * @param mixed $onlyGuest
     *
     * @return $this
     */
    protected function filterCustomers($onlyGuest = null)
    {
        if ((bool)$onlyGuest) {
            $this->subscriptionCollection->addFieldToFilter('customer_id', ['eq' => 0]);
        }
        return $this;
    }

    /**
     * @param \Emartech\Emarsys\Api\Data\SubscriptionInterface $subscription
     * @param string                                           $type
     *
     * @return bool
     * @throws \Exception
     */
    protected function changeSubscription($subscription, $type)
    {
        if ($subscription->getSubscriberEmail()) {
            /** @var \Magento\Newsletter\Model\Subscriber $subscriber */
            $subscriber = $this->subscriberFactory->create()->loadByEmail($subscription->getSubscriberEmail());

            if ($subscriber->getId()) {
                if ($subscriber->getStatus() === $type) {
                    return false;
                }
            } else {
                foreach ($subscription->getData() as $key => $value) {
                    $subscriber->setData($key, $value);
                }
            }

            $subscriber->setStatus($type);
            $subscriber->setStatusChanged(true);
            $subscriber->save();
            return true;
        }
        return false;
    }
}

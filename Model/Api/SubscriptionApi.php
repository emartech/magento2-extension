<?php

namespace Emartech\Emarsys\Model\Api;

use Emartech\Emarsys\Api\SubscriptionApiInterface;
use Magento\Newsletter\Model\ResourceModel\Subscriber\CollectionFactory as SubscriberCollectionFactory;
use Magento\Newsletter\Model\SubscriberFactory as SubscriberFactory;
use Magento\Newsletter\Model\Subscriber;

class SubscriptionApi implements SubscriptionApiInterface
{
    /**  @var SubscriberCollectionFactory */
    protected $subscriberCollectionFactory;

    /**  @var SubscriberFactory */
    protected $subscriberFactory;

    /**
     * Subscription constructor.
     * @param SubscriberCollectionFactory $subscriberCollectionFactory
     * @param SubscriberFactory $subscriberFactory
     */

    public function __construct(
        SubscriberCollectionFactory $subscriberCollectionFactory,
        SubscriberFactory $subscriberFactory
    )
    {
        $this->subscriberCollectionFactory = $subscriberCollectionFactory;
        $this->subscriberFactory = $subscriberFactory;
    }

    /**
     * @param int $page
     * @param int $page_size
     * @param string[] $emails
     * @param bool|null $subscribed
     * @param bool $with_customer
     * @return mixed[]
     */
    public function getList($page = 1, $page_size = 1000, $emails = [], $subscribed = null, $with_customer = false)
    {
        $subscriptions = $this->subscriberCollectionFactory
            ->create()
            ->addFieldToSelect(['customer_id', 'subscriber_email', 'subscriber_status', 'store_id'])
            ->removeFieldFromSelect('subscriber_id');

        if (!empty($emails)) {
            $this->addEmailFilter($emails, $subscriptions);
        }

        if (isset($subscribed) && $subscribed !== null) {
            $this->addSubscriptionStatusFilter($subscribed, $subscriptions);
        }

        if ($with_customer === false) {
            $this->addCustomerFilter($subscriptions);
        }

        $total_count = $subscriptions->count();

        $subscriptions
            ->setCurPage($page)
            ->setPageSize($page_size);

        $responseData = [[
            'subscriptions' => $subscriptions->getData(),
            'page' => $page,
            'page_size' => $page_size,
            'total_count' => $total_count
        ]];

        return $responseData;
    }

    /**
     * @param mixed $subscriptions
     * @return mixed
     */
    public function update($subscriptions)
    {
        foreach ($subscriptions as $subscription) {
            if ($subscription['subscriber_status'] === true) {
                $this->subscribe($subscription);
            } else {
                $this->unsubscribe($subscription['subscriber_email']);
            }
        }
        return 'OK';
    }

    private function subscribe($subscription)
    {
        $email = $subscription['subscriber_email'];
        $customerId = 0;
        if (array_key_exists('customer_id', $subscription)) {
            $customerId = $subscription['customer_id'];
        }

        $subscriber = $this->subscriberFactory->create()->loadByEmail($email);
        if ($subscriber->getId() && $subscriber->getStatus() == Subscriber::STATUS_SUBSCRIBED) {
            return false;
        }

        if (!$subscriber->getId()) {
            $subscriber->setSubscriberEmail($email);
            $subscriber->setCustomerId($customerId);
        }

        $subscriber->setStatus(Subscriber::STATUS_SUBSCRIBED);
        $subscriber->setStatusChanged(true);
        $subscriber->save();
        return true;
    }

    private function unsubscribe($email)
    {
        $subscriber = $this->subscriberFactory->create()->loadByEmail($email);
        if ($subscriber->getId() && $subscriber->getSubscriberStatus() != Subscriber::STATUS_UNSUBSCRIBED) {
            $subscriber->setSubscriberStatus(Subscriber::STATUS_UNSUBSCRIBED)->save();
            return true;
        }
        return false;
    }

    /**
     * @param $emails
     * @param $subscriptions
     */
    private function addEmailFilter($emails, $subscriptions)
    {
        $subscriptions->addFieldToFilter(
            'subscriber_email',
            ['in' => $emails]
        );
    }

    /**
     * @param $subscribed
     * @param $subscriptions
     */
    private function addSubscriptionStatusFilter($subscribed, $subscriptions): void
    {
        if ($subscribed === true) {
            $subscriptions->addFieldToFilter(
                'subscriber_status',
                ['eq' => '1']
            );
        } else {
            $subscriptions->addFieldToFilter(
                'subscriber_status',
                ['neq' => '1']
            );
        }
    }

    /**
     * @param $subscriptions
     */
    private function addCustomerFilter($subscriptions): void
    {
        $subscriptions->addFieldToFilter(
            'customer_id',
            ['eq' => '0']
        );
    }
}

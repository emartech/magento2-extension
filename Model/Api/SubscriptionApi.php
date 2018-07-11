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
  ) {
    $this->subscriberCollectionFactory = $subscriberCollectionFactory;
    $this->subscriberFactory = $subscriberFactory;
  }

  /**
   * @param int $page
   * @param int $page_size
   * @param string[] $emails
   * @return mixed[]
   */
  public function getList($page, $page_size, $emails = [])
  {
    $test = empty($emails);
    $subscriptions = $this->subscriberCollectionFactory
      ->create()
      ->addFieldToSelect(['customer_id', 'subscriber_email', 'subscriber_status'])
      ->removeFieldFromSelect('subscriber_id');

    $total_count = $this->subscriberCollectionFactory->create();
    if (!empty($emails)) {
      $subscriptions->addFieldToFilter(
        'subscriber_email',
        [ 'in' => $emails ]
      );
      $test = 1;
    }

    $total_count = $subscriptions->count();

    $subscriptions
      ->setCurPage($page)
      ->setPageSize($page_size);

    $responseData = [[
      'subscriptions' => $subscriptions->getData(),
      'page' => $page,
      'page_size' => $page_size,
      'total_count' => $total_count,
      'test' => $test
    ]];

    return $responseData;
  }

  /**
   * @param mixed $subscriptions
   * @return boolean
   */
  public function update($subscriptions)
  {
    foreach ($subscriptions as $subscription) {
      if ($subscription['status'] === true) {
        $this->subscribe($subscription);
      } else {
        $this->unsubscribe($subscription['email']);
      }
    }
  }

  private function subscribe($subscription)
  {
    $email = $subscription['email'];
    $customerId = 0;
    if (array_key_exists('customerId', $subscription)) {
      $customerId = $subscription['customerId'];
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
}

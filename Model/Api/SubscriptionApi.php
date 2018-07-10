<?php

namespace Emartech\Emarsys\Model\Api;
use Emartech\Emarsys\Api\SubscriptionApiInterface;
use Magento\Newsletter\Model\ResourceModel\Subscriber\CollectionFactory as SubscriberCollectionFactory;

class SubscriptionApi implements SubscriptionApiInterface
{
  /**  @var SubscriberCollectionFactory */
  protected $subscriberCollectionFactory;

  /**
   * Subscription constructor.
   * @param SubscriberCollectionFactory $subscriberCollectionFactory
   */

  public function __construct(
    SubscriberCollectionFactory $subscriberCollectionFactory
  ) {
    $this->subscriberCollectionFactory = $subscriberCollectionFactory;
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
      'subscribers' => $subscriptions->getData(),
      'page' => $page,
      'page_size' => $page_size,
      'total_count' => $total_count,
      'test' => $test
    ]];

    return $responseData;
  }
}

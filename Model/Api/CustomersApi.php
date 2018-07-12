<?php


namespace Emartech\Emarsys\Model\Api;


use Emartech\Emarsys\Api\CustomersApiInterface;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\Customer\Collection;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Newsletter\Model\SubscriberFactory;

class CustomersApi implements CustomersApiInterface
{

  /**
   * @var CustomerCollectionFactory
   */
  private $collectionFactory;
  /**
   * @var SubscriberFactory
   */
  private $subscriberFactory;

  public function __construct(
    CustomerCollectionFactory $collectionFactory,
    SubscriberFactory $subscriberFactory
  ) {

    $this->collectionFactory = $collectionFactory;
    $this->subscriberFactory = $subscriberFactory;
  }

  /**
   * @param int $page
   * @param int $page_size
   * @return mixed
   */
  public function get($page, $page_size)
  {
    /** @var Collection $customerCollection */
    $customerCollection = $this->collectionFactory->create()
      ->addAttributeToSelect(['is_subscribed'])
      ->setPage($page, $page_size);

    $customers = [];

    foreach ($customerCollection as $customer) {
      $customerData = $this->loadExtensionData($customer);
      $customerData['id'] = $customerData['entity_id'];
      $customers[] = $customerData;
    }

    $responseData = [[
      'customers' => $customers,
      'current_page' => $customerCollection->getCurPage(),
      'last_page' => $customerCollection->getLastPageNumber(),
      'page_size' => $customerCollection->getPageSize()
    ]];

    return $responseData;
  }

  private function loadExtensionData(Customer $customer)
  {
    $customerData = $customer->getData();

    if ($customer->getDefaultBillingAddress()) {
      $customerData['billing_address'] = $customer->getDefaultBillingAddress()->toArray();
    }
    if ($customer->getDefaultShippingAddress()) {
      $customerData['shipping_address'] = $customer->getDefaultShippingAddress()->toArray();
    }
    $subscription = $this->subscriberFactory->create()->loadByCustomerId($customer->getId());
    $customerData['subscription'] = $subscription->getStatus();

    return $customerData;
  }
}
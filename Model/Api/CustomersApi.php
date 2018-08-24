<?php

namespace Emartech\Emarsys\Model\Api;

/**
 * Class CustomersApi
 * @package Emartech\Emarsys\Model\Api
 */
class CustomersApi implements \Emartech\Emarsys\Api\CustomersApiInterface
{

    /**
     * @var array
     */
    private $addressFields = [];

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Emartech\Emarsys\Api\Data\CustomerInterfaceFactory
     */
    private $customerInterface;

    /**
     * @var \Emartech\Emarsys\Api\Data\CustomerAddressInterfaceFactory
     */
    private $customerAddressInterfaceFactory;

    /**
     * @var \Emartech\Emarsys\Api\Data\CustomersInterface
     */
    private $customersResponse;

    /**
     * CustomersApi constructor.
     *
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $collectionFactory
     * @param \Emartech\Emarsys\Api\Data\CustomerInterfaceFactory              $customerInterface
     * @param \Emartech\Emarsys\Api\Data\CustomerAddressInterfaceFactory       $customerAddressInterfaceFactory
     * @param \Emartech\Emarsys\Api\Data\CustomersApiResponseInterfaceFactory  $customersResponse
     */
    public function __construct(
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $collectionFactory,
        \Emartech\Emarsys\Api\Data\CustomerInterfaceFactory $customerInterface,
        \Emartech\Emarsys\Api\Data\CustomerAddressInterfaceFactory $customerAddressInterfaceFactory,
        \Emartech\Emarsys\Api\Data\CustomersApiResponseInterfaceFactory $customersResponse
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->customerInterface = $customerInterface;
        $this->customerAddressInterfaceFactory = $customerAddressInterfaceFactory;
        $this->customersResponse = $customersResponse;

        $customerAddressInterfaceReflection = new \ReflectionClass('\Emartech\Emarsys\Api\Data\CustomerAddressInterface');
        $this->addressFields = $customerAddressInterfaceReflection->getConstants();
    }

    /**
     * @param int  $page
     * @param int  $pageSize
     * @param null $websiteId
     *
     * @return \Emartech\Emarsys\Api\Data\CustomersApiResponseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($page, $pageSize, $websiteId = null)
    {
        /** @var \Magento\Customer\Model\ResourceModel\Customer\Collection $customerCollection */
        $customerCollection = $this->collectionFactory->create()
            ->addAttributeToSelect(['is_subscribed'])
            ->setPage($page, $pageSize);

        if ($websiteId) {
            $customerCollection->addAttributeToFilter('website_id', ['eq' => $websiteId]);
        }

        $this
            ->joinAddress($customerCollection, 'billing')
            ->joinAddress($customerCollection, 'shipping')
            ->joinSubscriptionStatus($customerCollection);

        $customerArray = [];

        foreach ($customerCollection as $customer) {
            $customerArray[] = $this->parseCustomer($customer);
        }

        return $this->customersResponse->create()
            ->setCurrentPage($customerCollection->getCurPage())
            ->setLastPage($customerCollection->getLastPageNumber())
            ->setPageSize($customerCollection->getPageSize())
            ->setCustomers($customerArray);
    }

    /**
     * @param \Magento\Customer\Model\Customer $customer
     *
     * @return \Emartech\Emarsys\Api\Data\CustomerInterface
     */
    protected function parseCustomer($customer)
    {
        /** @var \Emartech\Emarsys\Api\Data\CustomerInterface $customerItem */
        $customerItem = $this->customerInterface->create()
            ->setId($customer->getId())
            ->setBillingAddress($this->getAddressFromCustomer($customer, 'billing'))
            ->setShippingAddress($this->getAddressFromCustomer($customer, 'shipping'));

        foreach ($customer->getData() as $key => $value) {
            $customerItem->setData($key, $value);
        }

        return $customerItem;
    }

    /**
     * @param \Magento\Customer\Model\Customer $customer
     * @param string                           $addressType
     *
     * @return \Emartech\Emarsys\Api\Data\CustomerAddressInterface
     */
    protected function getAddressFromCustomer($customer, $addressType = 'billing')
    {
        /** @var \Emartech\Emarsys\Api\Data\CustomerAddressInterface $address */
        $address = $this->customerAddressInterfaceFactory->create();

        foreach ($customer->getData() as $key => $value) {
            if (strpos($key, $addressType) === 0) {
                $key = explode('.', $key);
                $key = array_pop($key);

                $address->setData($key, $value);
            }
        }

        return $address;
    }

    /**
     * @param \Magento\Customer\Model\ResourceModel\Customer\Collection $collection
     *
     * @return void
     */
    protected function joinSubscriptionStatus($collection)
    {
        $subscriptionTable = $collection->getResource()->getTable('newsletter_subscriber');
        $tableAlias = 'newsletter';

        $collection->getSelect()->joinLeft(
            [$tableAlias => $subscriptionTable],
            $tableAlias . '.customer_id = e.entity_id',
            ['accepts_marketing' => 'subscriber_status']
        );
    }

    /**
     * @param \Magento\Customer\Model\ResourceModel\Customer\Collection $customerCollection
     * @param string                                                    $addressType
     *
     * @return $this
     */
    protected function joinAddress($customerCollection, $addressType = 'billing')
    {
        $customerAddressEntityTable = $customerCollection->getResource()->getTable('customer_address_entity');
        $tableAlias = $addressType . '_address';

        $attributes = [];
        foreach ($this->addressFields as $addressConstantKey => $addressField) {
            $attributes[$addressType . '.' . $addressField] = $tableAlias . '.' . $addressField;
        }

        $customerCollection->getSelect()->joinLeft(
            [$tableAlias => $customerAddressEntityTable],
            $tableAlias . '.entity_id = e.default_' . $addressType,
            $attributes
        );

        return $this;
    }
}

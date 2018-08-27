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
     * @var \Magento\Customer\Model\ResourceModel\Customer\Collection
     */
    private $customerCollection;

    /**
     * @var string
     */
    private $customerAddressEntityTable;

    /**
     * @var string
     */
    private $subscriptionTable;

    /**
     * CustomersApi constructor.
     *
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $collectionFactory
     * @param \Emartech\Emarsys\Api\Data\CustomerInterfaceFactory              $customerInterface
     * @param \Emartech\Emarsys\Api\Data\CustomerAddressInterfaceFactory       $customerAddressInterfaceFactory
     * @param \Emartech\Emarsys\Api\Data\CustomersApiResponseInterfaceFactory  $customersResponse
     *
     * @throws \ReflectionException
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

        $this->customerCollection = $this->collectionFactory->create();
        $this->customerAddressEntityTable = $this->customerCollection->getResource()->getTable('customer_address_entity');
        $this->subscriptionTable = $this->customerCollection->getResource()->getTable('newsletter_subscriber');
    }

    /**
     * @param int   $page
     * @param int   $pageSize
     * @param mixed $websiteId
     * @param mixed $storeId
     *
     * @return \Emartech\Emarsys\Api\Data\CustomersApiResponseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($page, $pageSize, $websiteId = null, $storeId = null)
    {
        $this
            ->filterMixedParam($websiteId, 'website_id')
            ->filterMixedParam($storeId, 'store_id')
            ->joinAddress('billing')
            ->joinAddress('shipping')
            ->joinSubscriptionStatus()
            ->setPage($page, $pageSize);

        return $this->customersResponse->create()
            ->setCurrentPage($this->customerCollection->getCurPage())
            ->setLastPage($this->customerCollection->getLastPageNumber())
            ->setPageSize($this->customerCollection->getPageSize())
            ->setCustomers($this->handleCustomers());
    }

    /**
     * @param int $page
     * @param int $pageSize
     *
     * @return $this
     */
    protected function setPage($page, $pageSize)
    {
        $this->customerCollection->setPage($page, $pageSize);
        return $this;
    }

    /**
     * @param mixed  $param
     * @param string $type
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function filterMixedParam($param, $type)
    {
        if ($param) {
            if (!is_array($param)) {
                $param = explode(',', $param);
            }
            $this->customerCollection->addAttributeToFilter($type, ['in' => $param]);
        }
        return $this;
    }

    /**
     * @return array
     */
    protected function handleCustomers()
    {
        $customerArray = [];
        foreach ($this->customerCollection as $customer) {
            $customerArray[] = $this->parseCustomer($customer);
        }

        return $customerArray;
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
     * @return $this
     */
    protected function joinSubscriptionStatus()
    {
        $tableAlias = 'newsletter';

        $this->customerCollection->getSelect()->joinLeft(
            [$tableAlias => $this->subscriptionTable],
            $tableAlias . '.customer_id = e.entity_id',
            ['accepts_marketing' => 'subscriber_status']
        );

        return $this;
    }

    /**
     * @param string $addressType
     *
     * @return $this
     */
    protected function joinAddress($addressType = 'billing')
    {
        $tableAlias = $addressType . '_address';

        $attributes = [];
        foreach ($this->addressFields as $addressConstantKey => $addressField) {
            $attributes[$addressType . '.' . $addressField] = $tableAlias . '.' . $addressField;
        }

        $this->customerCollection->getSelect()->joinLeft(
            [$tableAlias => $this->customerAddressEntityTable],
            $tableAlias . '.entity_id = e.default_' . $addressType,
            $attributes
        );

        return $this;
    }
}

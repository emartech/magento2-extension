<?php

namespace Emartech\Emarsys\Model\Api;

use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Customer\Model\ResourceModel\Customer\Collection;
use Magento\Customer\Model\Customer;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Emartech\Emarsys\Api\CustomersApiInterface;
use Emartech\Emarsys\Api\Data\CustomerInterfaceFactory;
use Emartech\Emarsys\Api\Data\CustomerInterface;
use Emartech\Emarsys\Api\Data\CustomerAddressInterfaceFactory;
use Emartech\Emarsys\Api\Data\CustomerAddressInterface;
use Emartech\Emarsys\Api\Data\CustomersApiResponseInterfaceFactory;
use Emartech\Emarsys\Api\Data\CustomersApiResponseInterface;

/**
 * Class CustomersApi
 * @package Emartech\Emarsys\Model\Api
 */
class CustomersApi implements CustomersApiInterface
{

    /**
     * @var array
     */
    private $addressFields = [
        'prefix',
        'firstname',
        'middlename',
        'lastname',
        'suffix',
        'company',
        'street',
        'city',
        'country_id',
        'region',
        'postcode',
        'telephone',
        'fax',
    ];

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var CustomerInterfaceFactory
     */
    private $customerFactory;

    /**
     * @var CustomerAddressInterfaceFactory
     */
    private $customerAddressFactory;

    /**
     * @var CustomersApiResponseInterfaceFactory
     */
    private $customersResponseFactory;

    /**
     * @var Collection
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
     * @var ContainerBuilder
     */
    private $containerBuilder;

    /**
     * CustomersApi constructor.
     *
     * @param CollectionFactory                    $collectionFactory
     * @param CustomerInterfaceFactory             $customerFactory
     * @param CustomerAddressInterfaceFactory      $customerAddressFactory
     * @param CustomersApiResponseInterfaceFactory $customersResponseFactory
     * @param ContainerBuilder                     $containerBuilder
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        CustomerInterfaceFactory $customerFactory,
        CustomerAddressInterfaceFactory $customerAddressFactory,
        CustomersApiResponseInterfaceFactory $customersResponseFactory,
        ContainerBuilder $containerBuilder
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->customerFactory = $customerFactory;
        $this->customerAddressFactory = $customerAddressFactory;
        $this->customersResponseFactory = $customersResponseFactory;
        $this->containerBuilder = $containerBuilder;
    }

    /**
     * @param int         $page
     * @param int         $pageSize
     * @param string|null $websiteId
     * @param string|null $storeId
     *
     * @return CustomersApiResponseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($page, $pageSize, $websiteId = null, $storeId = null)
    {
        $this
            ->initCollection()
            ->filterMixedParam($websiteId, 'website_id')
            ->filterMixedParam($storeId, 'store_id')
            ->joinAddress('billing')
            ->joinAddress('shipping')
            ->joinSubscriptionStatus()
            ->setPage($page, $pageSize);

        return $this->customersResponseFactory->create()
            ->setCurrentPage($this->customerCollection->getCurPage())
            ->setLastPage($this->customerCollection->getLastPageNumber())
            ->setPageSize($this->customerCollection->getPageSize())
            ->setTotalCount($this->customerCollection->getSize())
            ->setCustomers($this->handleCustomers());
    }

    /**
     * @return $this
     */
    private function initCollection()
    {
        $this->customerCollection = $this->collectionFactory->create();
        $this->customerAddressEntityTable = $this->customerCollection->getResource()
            ->getTable('customer_address_entity');
        $this->subscriptionTable = $this->customerCollection->getResource()->getTable('newsletter_subscriber');

        return $this;
    }

    /**
     * @param int $page
     * @param int $pageSize
     *
     * @return $this
     */
    private function setPage($page, $pageSize)
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
    private function filterMixedParam($param, $type)
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
    private function handleCustomers()
    {
        $customerArray = [];
        foreach ($this->customerCollection as $customer) {
            $customerArray[] = $this->parseCustomer($customer);
        }

        return $customerArray;
    }

    /**
     * @param Customer $customer
     *
     * @return CustomerInterface
     */
    private function parseCustomer($customer)
    {
        /** @var CustomerInterface $customerItem */
        $customerItem = $this->customerFactory->create()
            ->setId($customer->getId())
            ->setBillingAddress($this->getAddressFromCustomer($customer, 'billing'))
            ->setShippingAddress($this->getAddressFromCustomer($customer, 'shipping'));

        foreach ($customer->getData() as $key => $value) {
            $customerItem->setData($key, $value);
        }

        return $customerItem;
    }

    /**
     * @param Customer $customer
     * @param string   $addressType
     *
     * @return CustomerAddressInterface
     */
    private function getAddressFromCustomer($customer, $addressType = 'billing')
    {
        /** @var CustomerAddressInterface $address */
        $address = $this->customerAddressFactory->create();

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
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function joinSubscriptionStatus()
    {
        $tableAlias = 'newsletter';

        $this->customerCollection->joinTable(
            [$tableAlias => $this->subscriptionTable],
            'customer_id = entity_id',
            ['accepts_marketing' => 'subscriber_status'],
            null,
            'left'
        );

        return $this;
    }

    /**
     * @param string $addressType
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function joinAddress($addressType = 'billing')
    {
        $tableAlias = $addressType . '_address';

        $attributes = [];
        foreach ($this->addressFields as $addressConstantKey => $addressField) {
            $attributes[$addressType . '.' . $addressField] = $addressField;
        }

        $this->customerCollection->joinTable(
            [$tableAlias => $this->customerAddressEntityTable],
            'entity_id = default_' . $addressType,
            $attributes,
            null,
            'left'
        );

        return $this;
    }
}

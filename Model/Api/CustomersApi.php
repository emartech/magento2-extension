<?php

namespace Emartech\Emarsys\Model\Api;

use Emartech\Emarsys\Api\AttributesApiInterface;
use Emartech\Emarsys\Api\CustomersApiInterface;
use Emartech\Emarsys\Api\Data\ConfigInterface;
use Emartech\Emarsys\Api\Data\ConfigInterfaceFactory;
use Emartech\Emarsys\Api\Data\CustomerAddressInterface;
use Emartech\Emarsys\Api\Data\CustomerAddressInterfaceFactory;
use Emartech\Emarsys\Api\Data\CustomerInterfaceFactory;
use Emartech\Emarsys\Api\Data\CustomersApiResponseInterface;
use Emartech\Emarsys\Api\Data\CustomersApiResponseInterfaceFactory;
use Emartech\Emarsys\Api\Data\ExtraFieldsInterfaceFactory;
use Emartech\Emarsys\Helper\LinkField;
use Emartech\Emarsys\Model\ResourceModel\Api\Customer as CustomerResource;
use Emartech\Emarsys\Model\ResourceModel\Api\CustomerAddress as CustomerAddressResource;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Config\Share as ConfigShare;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\Customer\Collection as CustomerCollection;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Framework\Data\Collection as DataCollection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Webapi\Exception as WebApiException;

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
     * @var array
     */
    private $extraAddressFields = [];

    private $fields = [
        'id',
        'email',
        'website_id',
        'group_id',
        'store_id',
        'is_active',
        'prefix',
        'firstname',
        'middlename',
        'lastname',
        'suffix',
        'dob',
        'taxvat',
        'gender',
        'accepts_marketing',
        'created_at',
        'updated_at',
        'default_shipping',
        'default_billing',
    ];

    /**
     * @var array
     */
    private $extraFields = [];

    /**
     * @var ConfigShare
     */
    private $configShare;

    /**
     * @var bool|int
     */
    private $websiteId = false;

    /**
     * @var CustomerCollectionFactory
     */
    private $customerCollectionFactory;

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
     * @var CustomerCollection
     */
    private $customerCollection;

    /**
     * @var string
     */
    private $customerAddressEntityTable;

    /**
     * @var CustomerResource
     */
    private $customerResource;

    /**
     * @var CustomerAddressResource
     */
    private $customerAddressResource;

    /**
     * @var ConfigInterfaceFactory
     */
    private $configFactory;

    /**
     * @var ExtraFieldsInterfaceFactory
     */
    private $extraFieldsFactory;

    /**
     * @var int
     */
    private $minId = 0;

    /**
     * @var int
     */
    private $maxId = 0;

    /**
     * @var int
     */
    private $numberOfItems = 0;

    /**
     * @var string
     */
    private $linkField;

    /**
     * @var LinkField
     */
    private $linkFieldHelper;

    /**
     * @var array
     */
    private $attributeData;

    /**
     * @var array
     */
    private $addressAttributeData;

    /**
     * CustomersApi constructor.
     *
     * @param CustomerCollectionFactory            $customerCollectionFactory
     * @param CustomerInterfaceFactory             $customerFactory
     * @param CustomerAddressInterfaceFactory      $customerAddressFactory
     * @param CustomersApiResponseInterfaceFactory $customersResponseFactory
     * @param CustomerResource                     $customerResource
     * @param CustomerAddressResource              $customerAddressResource
     * @param ConfigInterfaceFactory               $configFactory
     * @param ExtraFieldsInterfaceFactory          $extraFieldsFactory
     * @param ConfigShare                          $configShare
     * @param LinkField                            $linkFieldHelper
     */
    public function __construct(
        CustomerCollectionFactory $customerCollectionFactory,
        CustomerInterfaceFactory $customerFactory,
        CustomerAddressInterfaceFactory $customerAddressFactory,
        CustomersApiResponseInterfaceFactory $customersResponseFactory,
        CustomerResource $customerResource,
        CustomerAddressResource $customerAddressResource,
        ConfigInterfaceFactory $configFactory,
        ExtraFieldsInterfaceFactory $extraFieldsFactory,
        ConfigShare $configShare,
        LinkField $linkFieldHelper
    ) {
        $this->configShare = $configShare;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->customerFactory = $customerFactory;
        $this->customerAddressFactory = $customerAddressFactory;
        $this->customersResponseFactory = $customersResponseFactory;
        $this->customerResource = $customerResource;
        $this->customerAddressResource = $customerAddressResource;
        $this->configFactory = $configFactory;
        $this->extraFieldsFactory = $extraFieldsFactory;
        $this->linkFieldHelper = $linkFieldHelper;
        $this->linkField = $this->linkFieldHelper->getEntityLinkField(CustomerInterface::class);
    }

    /**
     * @param int         $page
     * @param int         $pageSize
     * @param string|null $websiteId
     * @param string|null $storeId
     * @param bool|null   $onlyReg
     *
     * @return CustomersApiResponseInterface
     * @throws LocalizedException
     */
    public function get($page, $pageSize, $websiteId = null, $storeId = null, $onlyReg = null)
    {
        /** @var ConfigInterface $config */
        $config = $this->configFactory->create();

        if (!array_key_exists($websiteId, $config->getAvailableWebsites())) {
            throw new WebApiException(__('Invalid Website'));
        }

        $this
            ->handleWebsiteId($websiteId, $onlyReg)
            ->initCollection()
            ->handleIds($page, $pageSize)
            ->handleAttributeData($websiteId)
            ->handleAddressesAttributeData($websiteId)
            ->joinSubscriptionStatus()
            ->setWhere()
            ->setOrder();

        $lastPageNumber = ceil($this->numberOfItems / $pageSize);

        return $this->customersResponseFactory->create()
            ->setCurrentPage($page)
            ->setLastPage($lastPageNumber)
            ->setPageSize($pageSize)
            ->setTotalCount($this->numberOfItems)
            ->setCustomers($this->handleCustomers());
    }

    /**
     * @param string|null $websiteId
     * @param bool        $onlyReg
     *
     * @return $this
     */
    private function handleWebsiteId($websiteId = null, $onlyReg = false)
    {
        if ($onlyReg || $this->configShare->isWebsiteScope()) {
            $this->websiteId = $websiteId;
        }
        return $this;
    }

    /**
     * @return $this
     */
    private function initCollection()
    {
        /** @var CustomerCollection customerCollection */
        $this->customerCollection = $this->customerCollectionFactory->create();

        return $this;
    }

    /**
     * @param int $page
     * @param int $pageSize
     *
     * @return $this
     */
    private function handleIds($page, $pageSize)
    {
        $page--;
        $page *= $pageSize;

        $data = $this->customerResource->handleIds($page, $pageSize, $this->websiteId);

        $this->numberOfItems = $data['numberOfItems'];
        $this->minId = $data['minId'];
        $this->maxId = $data['maxId'];

        return $this;
    }

    /**
     * @param int $websiteId
     *
     * @return $this
     * @throws LocalizedException
     */
    private function handleAttributeData($websiteId)
    {
        /** @var ConfigInterface $config */
        $config = $this->configFactory->create();

        $customerAttributes = $config->getConfigValue(
            AttributesApiInterface::TYPE_CUSTOMER . ConfigInterface::ATTRIBUTE_CONFIG_POST_TAG,
            $websiteId
        );

        if (is_array($customerAttributes)) {
            $this->extraFields = array_diff($customerAttributes, $this->fields);
        }

        $this->attributeData = $this->customerResource
            ->getAttributeData($this->minId, $this->maxId, array_merge($this->fields, $this->extraFields));

        return $this;
    }

    /**
     * @param int $websiteId
     *
     * @return $this
     */
    private function handleAddressesAttributeData($websiteId)
    {
        /** @var ConfigInterface $config */
        $config = $this->configFactory->create();

        $customerAddressAttributes = $config->getConfigValue(
            AttributesApiInterface::TYPE_CUSTOMER_ADDRESS . ConfigInterface::ATTRIBUTE_CONFIG_POST_TAG,
            $websiteId
        );

        if (is_array($customerAddressAttributes)) {
            $this->extraAddressFields = array_diff($customerAddressAttributes, $this->addressFields);
        }

        $this->addressAttributeData = $this->customerAddressResource
            ->getAttributeData($this->minId, $this->maxId, array_merge($this->addressFields, $this->extraAddressFields));

        return $this;
    }

    /**
     * @return $this
     */
    protected function setWhere()
    {
        $this->customerCollection
            ->addFieldToFilter($this->linkField, ['from' => $this->minId])
            ->addFieldToFilter($this->linkField, ['to' => $this->maxId]);

        return $this;
    }

    /**
     * @return $this
     */
    // @codingStandardsIgnoreLine
    protected function setOrder()
    {
        $this->customerCollection
            ->groupByAttribute($this->linkField)
            ->setOrder($this->linkField, DataCollection::SORT_ORDER_ASC);

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
        $customerId = $customer->getId();

        /** @var CustomerInterface $customerItem */
        $customerItem = $this->customerFactory->create()
            ->setId($customer->getId())
            ->setBillingAddress($this->getAddressFromCustomer($customer->getId(), $customer->getData('default_billing')))
            ->setShippingAddress($this->getAddressFromCustomer($customer->getId(), $customer->getData('default_shipping')))
            ->setWebsiteId($customer->getWebsiteId())
            ->setStoreId($customer->getStoreId())
            ->setEmail($customer->getEmail())
            ->setGroupId($customer->getGroupId())
            ->setIsActive($customer->getData('is_active'))
            ->setPrefix($customer->getData('prefix'))
            ->setLastname($customer->getData('lastname'))
            ->setMiddlename($customer->getData('middlename'))
            ->setFirstname($customer->getData('firstname'))
            ->setSuffix($customer->getData('suffix'))
            ->setDob($customer->getData('dob'))
            ->setTaxvat($customer->getData('taxvat'))
            ->setGender($customer->getData('gender'))
            ->setAcceptsMarketing($customer->getData('accepts_marketing'))
            ->setCreatedAt($customer->getData('created_at'))
            ->setUpdatedAt($customer->getData('updated_at'));

        if ($this->extraFields) {
            $extraFields = [];
            foreach ($this->extraFields as $field) {
                $extraFields[] = $this->extraFieldsFactory->create()
                    ->setKey($field)
                    ->setValue($this->handleWebsiteData($customerId, $field));
            }
            $customerItem->setExtraFields($extraFields);
        }

        return $customerItem;
    }

    /**
     * @param int    $customerId
     * @param string $attributeCode
     *
     * @return string|null
     */
    private function handleWebsiteData($customerId, $attributeCode)
    {
        if (array_key_exists($customerId, $this->attributeData)
            && array_key_exists($attributeCode, $this->attributeData[$customerId])
        ) {
            return $this->attributeData[$customerId][$attributeCode];
        }

        return null;
    }

    /**
     * @param int    $customerId
     * @param int    $addressId
     * @param string $attributeCode
     *
     * @return string|null
     */
    private function handleAddressWebsiteData($customerId, $addressId, $attributeCode)
    {
        if (array_key_exists($customerId, $this->addressAttributeData)
            && array_key_exists($addressId, $this->addressAttributeData[$customerId])
            && array_key_exists($attributeCode, $this->addressAttributeData[$customerId][$addressId])
        ) {
            return $this->addressAttributeData[$customerId][$addressId][$attributeCode];
        }

        return null;
    }

    /**
     * @param int $customerId
     * @param int $addressId
     *
     * @return CustomerAddressInterface
     */
    private function getAddressFromCustomer($customerId, $addressId)
    {
        /** @var CustomerAddressInterface $address */
        $addressItem = $this->customerAddressFactory->create()
            ->setFirstname($this->handleAddressWebsiteData($customerId, $addressId, 'firstname'))
            ->setSuffix($this->handleAddressWebsiteData($customerId, $addressId, 'suffix'))
            ->setMiddlename($this->handleAddressWebsiteData($customerId, $addressId, 'middlename'))
            ->setLastname($this->handleAddressWebsiteData($customerId, $addressId, 'lastname'))
            ->setPrefix($this->handleAddressWebsiteData($customerId, $addressId, 'prefix'))
            ->setCity($this->handleAddressWebsiteData($customerId, $addressId, 'city'))
            ->setCompany($this->handleAddressWebsiteData($customerId, $addressId, 'company'))
            ->setCountryId($this->handleAddressWebsiteData($customerId, $addressId, 'country_id'))
            ->setFax($this->handleAddressWebsiteData($customerId, $addressId, 'fax'))
            ->setPostcode($this->handleAddressWebsiteData($customerId, $addressId, 'postcode'))
            ->setRegion($this->handleAddressWebsiteData($customerId, $addressId, 'region'))
            ->setStreet($this->handleAddressWebsiteData($customerId, $addressId, 'street'))
            ->setTelephone($this->handleAddressWebsiteData($customerId, $addressId, 'telephone'));

        if ($this->extraAddressFields) {
            $extraFields = [];
            foreach ($this->extraAddressFields as $field) {
                $extraFields[] = $this->extraFieldsFactory->create()
                    ->setKey($field)
                    ->setValue($this->handleAddressWebsiteData($customerId, $addressId, $field));
            }
            $addressItem->setExtraFields($extraFields);
        }

        return $addressItem;
    }

    /**
     * @return $this
     */
    private function joinSubscriptionStatus()
    {
        $this->customerResource->joinSubscriptionStatus($this->customerCollection);

        return $this;
    }
}

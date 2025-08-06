<?php
/**
 * Copyright ©2019 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Helper;

use Emartech\Emarsys\Api\AttributesApiInterface;
use Emartech\Emarsys\Api\Data\ConfigInterface;
use Emartech\Emarsys\Api\Data\ConfigInterfaceFactory;
use Emartech\Emarsys\Api\Data\CustomerAddressInterface;
use Emartech\Emarsys\Api\Data\CustomerAddressInterfaceFactory;
use Emartech\Emarsys\Api\Data\CustomerInterface;
use Emartech\Emarsys\Api\Data\CustomerInterfaceFactory;
use Emartech\Emarsys\Api\Data\ExtraFieldsInterfaceFactory;
use Emartech\Emarsys\Model\ResourceModel\Api\Customer as CustomerResource;
use Emartech\Emarsys\Model\ResourceModel\Api\CustomerAddress as CustomerAddressResource;
use Magento\Customer\Model\Customer as CustomerModel;
use Magento\Customer\Model\ResourceModel\Customer\Collection as CustomerCollection;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Customer extends AbstractHelper
{
    /**
     * @var string[]
     */
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
     * @var null|string[]
     */
    private $extraFields = null;

    /**
     * @var string[]
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
     * @var null|string[]
     */
    private $extraAddressFields = null;

    /**
     * @var ConfigInterfaceFactory
     */
    private $configFactory;

    /**
     * @var CustomerResource
     */
    private $customerResource;

    /**
     * @var CustomerAddressResource
     */
    private $customerAddressResource;

    /**
     * @var CustomerInterfaceFactory
     */
    private $customerFactory;

    /**
     * @var array
     */
    private $customerAttributeData = [];

    /**
     * @var array
     */
    private $customerAttributeValues = [];

    /**
     * @var array
     */
    private $customerAddressAttributeData = [];

    /**
     * @var array
     */
    private $customerAddressAttributeValues = [];

    /**
     * @var CustomerAddressInterfaceFactory
     */
    private $customerAddressFactory;

    /**
     * @var ExtraFieldsInterfaceFactory
     */
    private $extraFieldsFactory;

    /**
     * @var CustomerCollectionFactory
     */
    private $customerCollectionFactory;

    /**
     * @var RpTokenHelper
     */
    private $tokenHelper;

    /**
     * @var CustomerCollection|null
     */
    private $customerCollection = null;

    /**
     * @param ConfigInterfaceFactory          $configFactory
     * @param CustomerResource                $customerResource
     * @param CustomerAddressResource         $customerAddressResource
     * @param CustomerInterfaceFactory        $customerFactory
     * @param CustomerAddressInterfaceFactory $customerAddressFactory
     * @param ExtraFieldsInterfaceFactory     $extraFieldsFactory
     * @param CustomerCollectionFactory       $customerCollectionFactory
     * @param RpTokenHelper                   $tokenHelper
     * @param Context                         $context
     */
    public function __construct(
        ConfigInterfaceFactory $configFactory,
        CustomerResource $customerResource,
        CustomerAddressResource $customerAddressResource,
        CustomerInterfaceFactory $customerFactory,
        CustomerAddressInterfaceFactory $customerAddressFactory,
        ExtraFieldsInterfaceFactory $extraFieldsFactory,
        CustomerCollectionFactory $customerCollectionFactory,
        RpTokenHelper $tokenHelper,
        Context $context
    ) {
        $this->configFactory = $configFactory;
        $this->customerResource = $customerResource;
        $this->customerAddressResource = $customerAddressResource;
        $this->customerFactory = $customerFactory;
        $this->customerAddressFactory = $customerAddressFactory;
        $this->extraFieldsFactory = $extraFieldsFactory;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->tokenHelper = $tokenHelper;

        parent::__construct($context);
    }

    /**
     * InitCollection
     *
     * @param int|null $websiteId
     *
     * @return Customer
     */
    public function initCollection(?int $websiteId = null): Customer
    {
        $this->customerCollection = $this->customerCollectionFactory->create();

        if ($websiteId) {
            $this->customerCollection->addFieldToFilter(
                'website_id',
                ['eq' => $websiteId]
            );
        }

        return $this;
    }

    /**
     * GetOneCustomer
     *
     * @param int      $customerId
     * @param int|null $websiteId
     * @param bool     $toArray
     *
     * @return array|CustomerInterface|false
     */
    public function getOneCustomer(int $customerId, ?int $websiteId = null, bool $toArray = false)
    {
        $this
            ->initCollection($websiteId)
            ->setWhere('entity_id', $customerId, $customerId, null)
            ->joinSubscriptionStatus($websiteId)
            ->getCustomersAttributeData($customerId, $customerId, $websiteId)
            ->getCustomersAddressesAttributeData($customerId, $customerId, $websiteId);

        /** @var CustomerModel $customer */
        $customer = $this->getCustomerCollection()->fetchItem();

        if ($customer instanceof CustomerModel) {
            return $this->buildCustomerObject($customer, $websiteId, $toArray);
        }

        return false;
    }

    /**
     * HandleIds
     *
     * @param int      $page
     * @param int      $pageSize
     * @param int|null $websiteId
     *
     * @return array
     */
    public function handleIds(int $page, int $pageSize, ?int $websiteId = null): array
    {
        return $this->customerResource->handleIds($page, $pageSize, $websiteId);
    }

    /**
     * JoinSubscriptionStatus
     *
     * @param int|null $websiteId
     *
     * @return Customer
     */
    public function joinSubscriptionStatus(?int $websiteId = null): Customer
    {
        $this->customerResource->joinSubscriptionStatus(
            $this->customerCollection,
            $websiteId
        );

        return $this;
    }

    /**
     * SetWhere
     *
     * @param string   $linkField
     * @param int      $min
     * @param int      $max
     * @param int|null $websiteId
     *
     * @return Customer
     */
    public function setWhere(string $linkField, int $min, int $max, ?int $websiteId = null): Customer
    {
        $this->customerCollection
            ->addFieldToFilter($linkField, ['from' => $min])
            ->addFieldToFilter($linkField, ['to' => $max]);
        if ($websiteId) {
            $this->customerCollection->addFieldToFilter('website_id', ['eq' => $websiteId]);
        }

        return $this;
    }

    /**
     * SetOrder
     *
     * @param string $linkField
     * @param string $direction
     *
     * @return Customer
     */
    public function setOrder(string $linkField, string $direction): Customer
    {
        $this->customerCollection
            ->groupByAttribute($linkField)
            ->setOrder($linkField, $direction);

        return $this;
    }

    /**
     * GetCustomerCollection
     *
     * @return CustomerCollection
     */
    public function getCustomerCollection(): CustomerCollection
    {
        return $this->customerCollection;
    }

    /**
     * GetCustomerFields
     *
     * @return string[]
     */
    public function getCustomerFields(): array
    {
        return $this->fields;
    }

    /**
     * GetCustomerExtraFields
     *
     * @param int|null $websiteId
     *
     * @return array
     */
    public function getCustomerExtraFields(?int $websiteId = null): array
    {
        if (null == $this->extraFields) {
            $this->extraFields = [];

            $config = $this->configFactory->create();

            $customerAttributes = $config->getConfigValue(
                AttributesApiInterface::TYPE_CUSTOMER . ConfigInterface::ATTRIBUTE_CONFIG_POST_TAG,
                $websiteId
            );

            if (is_array($customerAttributes)) {
                $this->extraFields = $customerAttributes;
            }
        }

        return $this->extraFields;
    }

    /**
     * GetCustomerAddressFields
     *
     * @return string[]
     */
    public function getCustomerAddressFields(): array
    {
        return $this->addressFields;
    }

    /**
     * GetCustomerAddressExtraFields
     *
     * @param int|null $websiteId
     *
     * @return string[]
     */
    public function getCustomerAddressExtraFields(?int $websiteId = null): array
    {
        if (null == $this->extraAddressFields) {
            $this->extraAddressFields = [];

            $config = $this->configFactory->create();

            $customerAddressAttributes = $config->getConfigValue(
                AttributesApiInterface::TYPE_CUSTOMER_ADDRESS . ConfigInterface::ATTRIBUTE_CONFIG_POST_TAG,
                $websiteId
            );

            if (is_array($customerAddressAttributes)) {
                $this->extraAddressFields = $customerAddressAttributes;
            }
        }

        return $this->extraAddressFields;
    }

    /**
     * GetCustomersAttributeData
     *
     * @param int        $minId
     * @param int        $maxId
     * @param int|null   $websiteId
     * @param array|null $fields
     *
     * @return Customer
     */
    public function getCustomersAttributeData(
        int $minId,
        int $maxId,
        ?int $websiteId = null,
        ?array $fields = null
    ): Customer {
        if (!$fields) {
            $fields = array_merge(
                $this->getCustomerFields(),
                $this->getCustomerExtraFields($websiteId)
            );
        }

        $data = $this->customerResource->getAttributeData(
            $minId,
            $maxId,
            $fields,
            $websiteId
        );

        if (isset($data['attribute_data'])) {
            $this->customerAttributeData = $data['attribute_data'];
        }
        if (isset($data['attribute_values'])) {
            $this->customerAttributeValues = $data['attribute_values'];
        }

        return $this;
    }

    /**
     * GetCustomersAddressesAttributeData
     *
     * @param int        $minId
     * @param int        $maxId
     * @param int|null   $websiteId
     * @param array|null $fields
     *
     * @return Customer
     */
    public function getCustomersAddressesAttributeData(
        int $minId,
        int $maxId,
        ?int $websiteId = null,
        ?array $fields = null
    ): Customer {
        if (!$fields) {
            $fields = array_merge(
                $this->getCustomerAddressFields(),
                $this->getCustomerAddressExtraFields(
                    $websiteId
                )
            );
        }

        $data = $this->customerAddressResource->getAttributeData(
            $minId,
            $maxId,
            $fields,
            $websiteId
        );

        if (isset($data['attribute_data'])) {
            $this->customerAddressAttributeData = $data['attribute_data'];
        }
        if (isset($data['attribute_values'])) {
            $this->customerAddressAttributeValues = $data['attribute_values'];
        }

        return $this;
    }

    /**
     * HandleWebsiteData
     *
     * @param int    $customerId
     * @param string $attributeCode
     *
     * @return string|null
     */
    private function handleWebsiteData(int $customerId, string $attributeCode): ?string
    {
        if (array_key_exists($customerId, $this->customerAttributeData)
            && array_key_exists(
                $attributeCode,
                $this->customerAttributeData[$customerId]
            )
        ) {
            return $this->customerAttributeData[$customerId][$attributeCode];
        }

        return null;
    }

    /**
     * GetAttributeValue
     *
     * @param string $attributeCode
     * @param string $value
     * @param int    $storeId
     *
     * @return mixed|null
     */
    private function getAttributeValue(string $attributeCode, string $value, int $storeId = 0)
    {
        if (array_key_exists($storeId, $this->customerAttributeValues) &&
            array_key_exists(
                $attributeCode,
                $this->customerAttributeValues[$storeId]
            ) &&
            array_key_exists(
                $value,
                $this->customerAttributeValues[$storeId][$attributeCode]
            )
        ) {
            return $this->customerAttributeValues[$storeId][$attributeCode][$value];
        }

        return null;
    }

    /**
     * HandleAddressWebsiteData
     *
     * @param int    $customerId
     * @param int    $addressId
     * @param string $attributeCode
     *
     * @return string|null
     */
    private function handleAddressWebsiteData(int $customerId, int $addressId, string $attributeCode): ?string
    {

        if (array_key_exists($customerId, $this->customerAddressAttributeData)
            && array_key_exists(
                $addressId,
                $this->customerAddressAttributeData[$customerId]
            )
            && array_key_exists(
                $attributeCode,
                $this->customerAddressAttributeData[$customerId][$addressId]
            )
        ) {
            return $this->customerAddressAttributeData[$customerId][$addressId][$attributeCode];
        }

        return null;
    }

    /**
     * GetAddressAttributeValue
     *
     * @param string $attributeCode
     * @param string $value
     * @param int    $storeId
     *
     * @return mixed|null
     */
    private function getAddressAttributeValue(string $attributeCode, string $value, int $storeId = 0)
    {
        if (array_key_exists($storeId, $this->customerAddressAttributeValues) &&
            array_key_exists(
                $attributeCode,
                $this->customerAddressAttributeValues[$storeId]
            ) &&
            array_key_exists(
                $value,
                $this->customerAddressAttributeValues[$storeId][$attributeCode]
            )
        ) {
            return $this->customerAddressAttributeValues[$storeId][$attributeCode][$value];
        }

        return null;
    }

    /**
     * BuildCustomerObject
     *
     * @param CustomerModel $customer
     * @param int|null      $websiteId
     * @param bool          $toArray
     *
     * @return array|CustomerInterface|null
     */
    public function buildCustomerObject(CustomerModel $customer, ?int $websiteId = null, bool $toArray = false)
    {
        $billingAddress = $this->getAddressFromCustomer(
            $customer->getId(),
            $customer->getData('default_billing'),
            $websiteId,
            $toArray
        );

        $shippingAddress = $this->getAddressFromCustomer(
            $customer->getId(),
            $customer->getData('default_shipping'),
            $websiteId,
            $toArray
        );

        if ($toArray) {
            $billingAddress = $billingAddress->getData();
            $shippingAddress = $shippingAddress->getData();
        }

        $rpToken = $this->tokenHelper->decryptRpToken($customer->getRpToken() ?? '');

        /** @var CustomerInterface $customerItem */
        $customerItem = $this->customerFactory
            ->create()
            ->setId($customer->getId())
            ->setRpToken($rpToken)
            ->setRpTokenCreatedAt($customer->getRpTokenCreatedAt())
            ->setBillingAddress($billingAddress)
            ->setShippingAddress($shippingAddress)
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
            ->setAcceptsMarketing($customer->getData('accepts_marketing') ?? 0)
            ->setCreatedAt($customer->getData('created_at'))
            ->setUpdatedAt($customer->getData('updated_at'));

        $extraFields = [];
        if ($this->getCustomerExtraFields($websiteId)) {
            foreach ($this->getCustomerExtraFields($websiteId) as $field) {
                $value = $this->handleWebsiteData($customer->getId(), $field);
                if ($value) {
                    $textValue = $this->getAttributeValue($field, $value);
                    $extraField = $this->extraFieldsFactory
                        ->create()
                        ->setKey($field)
                        ->setValue($value)
                        ->setTextValue($textValue);

                    if ($toArray) {
                        $extraField = $extraField->getData();
                    }

                    $extraFields[] = $extraField;
                }
            }
        }
        $customerItem->setExtraFields($extraFields);

        if ($toArray) {
            $customerItem = $customerItem->getData();
        }

        return $customerItem;
    }

    /**
     * GetAddressFromCustomer
     *
     * @param int      $customerId
     * @param int|null $addressId
     * @param int|null $websiteId
     * @param bool     $toArray
     *
     * @return CustomerAddressInterface|array
     */
    private function getAddressFromCustomer(
        int $customerId,
        ?int $addressId = null,
        ?int $websiteId = null,
        bool $toArray = false
    ) {
        /** @var CustomerAddressInterface $address */
        $addressItem = $this->customerAddressFactory->create();

        $parentId = null;
        if ($addressId) {
            $parentId = $this->handleAddressWebsiteData($customerId, $addressId, 'parent_id');
        }

        if ($parentId) {
            $addressItem
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

            $extraFields = [];
            if ($this->getCustomerAddressExtraFields($websiteId)) {
                foreach ($this->getCustomerAddressExtraFields($websiteId) as $field) {
                    $value = $this->handleAddressWebsiteData(
                        $customerId,
                        $addressId,
                        $field
                    );
                    if ($value) {
                        $textValue = $this->getAddressAttributeValue($field, $value);
                        $extraField = $this->extraFieldsFactory
                            ->create()
                            ->setKey($field)
                            ->setValue($value)
                            ->setTextValue($textValue);

                        if ($toArray) {
                            $extraField = $extraField->getData();
                        }

                        $extraFields[] = $extraField;
                    }
                }
            }
            $addressItem->setExtraFields($extraFields);
        }

        return $addressItem;
    }
}

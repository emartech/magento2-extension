<?php

namespace Emartech\Emarsys\Api\Data;

interface CustomerInterface
{
    public const ID_KEY                = 'id';
    public const EMAIL_KEY             = 'email';
    public const WEBSITE_ID_KEY        = 'website_id';
    public const GROUP_ID_KEY          = 'group_id';
    public const STORE_ID_KEY          = 'store_id';
    public const IS_ACTIVE_KEY         = 'is_active';
    public const PREFIX_KEY            = 'prefix';
    public const FIRSTNAME_KEY         = 'firstname';
    public const MIDDLENAME_KEY        = 'middlename';
    public const LASTNAME_KEY          = 'lastname';
    public const SUFFIX_KEY            = 'suffix';
    public const DOB_KEY               = 'dob';
    public const TAXVAT_KEY            = 'taxvat';
    public const GENDER_KEY            = 'gender';
    public const BILLING_ADDRESS_KEY   = 'billing_address';
    public const SHIPPING_ADDRESS_KEY  = 'shipping_address';
    public const ACCEPTS_MARKETING_KEY = 'accepts_marketing';
    public const CREATED_AT_KEY        = 'created_at';
    public const UPDATED_AT_KEY        = 'updated_at';
    public const EXTRA_FIELDS          = 'extra_fields';

    /**
     * GetId
     *
     * @return int
     */
    public function getId(): int;

    /**
     * SetId
     *
     * @param int $id
     *
     * @return \Emartech\Emarsys\Api\Data\CustomerInterface
     */
    public function setId(int $id): CustomerInterface;

    /**
     * GetEmail
     *
     * @return string|null
     */
    public function getEmail(): ?string;

    /**
     * SetEmail
     *
     * @param string|null $email
     *
     * @return \Emartech\Emarsys\Api\Data\CustomerInterface
     */
    public function setEmail(?string $email = null): CustomerInterface;

    /**
     * GetWebsiteId
     *
     * @return int|null
     */
    public function getWebsiteId(): ?int;

    /**
     * SetWebsiteId
     *
     * @param int|null $websiteId
     *
     * @return \Emartech\Emarsys\Api\Data\CustomerInterface
     */
    public function setWebsiteId(?int $websiteId = null): CustomerInterface;

    /**
     * GetGroupId
     *
     * @return int|null
     */
    public function getGroupId(): ?int;

    /**
     * SetGroupId
     *
     * @param int|null $groupId
     *
     * @return \Emartech\Emarsys\Api\Data\CustomerInterface
     */
    public function setGroupId(?int $groupId = null): CustomerInterface;

    /**
     * GetStoreId
     *
     * @return int|null
     */
    public function getStoreId(): ?int;

    /**
     * GetExtraFields
     *
     * @return \Emartech\Emarsys\Api\Data\ExtraFieldsInterface[]|null
     */
    public function getExtraFields(): ?array;

    /**
     * SetStoreId
     *
     * @param int|null $storeId
     *
     * @return \Emartech\Emarsys\Api\Data\CustomerInterface
     */
    public function setStoreId(?int $storeId = null): CustomerInterface;

    /**
     * GetIsActive
     *
     * @return int|null
     */
    public function getIsActive(): ?int;

    /**
     * SetIsActive
     *
     * @param int|null $isActive
     *
     * @return \Emartech\Emarsys\Api\Data\CustomerInterface
     */
    public function setIsActive(?int $isActive = null): CustomerInterface;

    /**
     * GetPrefix
     *
     * @return string|null
     */
    public function getPrefix(): ?string;

    /**
     * SetPrefix
     *
     * @param string|null $prefix
     *
     * @return \Emartech\Emarsys\Api\Data\CustomerInterface
     */
    public function setPrefix(?string $prefix = null): CustomerInterface;

    /**
     * GetFirstname
     *
     * @return string|null
     */
    public function getFirstname(): ?string;

    /**
     * SetFirstname
     *
     * @param string|null $firstName
     *
     * @return \Emartech\Emarsys\Api\Data\CustomerInterface
     */
    public function setFirstname(?string $firstName = null): CustomerInterface;

    /**
     * GetMiddlename
     *
     * @return string|null
     */
    public function getMiddlename(): ?string;

    /**
     * SetMiddlename
     *
     * @param string|null $middleName
     *
     * @return \Emartech\Emarsys\Api\Data\CustomerInterface
     */
    public function setMiddlename(?string $middleName = null): CustomerInterface;

    /**
     * GetLastname
     *
     * @return string|null
     */
    public function getLastname(): ?string;

    /**
     * SetLastname
     *
     * @param string|null $lastName
     *
     * @return \Emartech\Emarsys\Api\Data\CustomerInterface
     */
    public function setLastname(?string $lastName = null): CustomerInterface;

    /**
     * GetSuffix
     *
     * @return string|null
     */
    public function getSuffix(): ?string;

    /**
     * SetSuffix
     *
     * @param string|null $suffix
     *
     * @return \Emartech\Emarsys\Api\Data\CustomerInterface
     */
    public function setSuffix(?string $suffix = null): CustomerInterface;

    /**
     * GetDob
     *
     * @return string|null
     */
    public function getDob(): ?string;

    /**
     * SetDob
     *
     * @param string|null $dob
     *
     * @return \Emartech\Emarsys\Api\Data\CustomerInterface
     */
    public function setDob(?string $dob = null): CustomerInterface;

    /**
     * GetTaxvat
     *
     * @return string|null
     */
    public function getTaxvat(): ?string;

    /**
     * SetTaxvat
     *
     * @param string|null $taxVat
     *
     * @return \Emartech\Emarsys\Api\Data\CustomerInterface
     */
    public function setTaxvat(?string $taxVat = null): CustomerInterface;

    /**
     * GetGender
     *
     * @return int|null
     */
    public function getGender(): ?int;

    /**
     * SetGender
     *
     * @param int|null $gender
     *
     * @return \Emartech\Emarsys\Api\Data\CustomerInterface
     */
    public function setGender(?int $gender = null): CustomerInterface;

    /**
     * GetBillingAddress
     *
     * @return \Emartech\Emarsys\Api\Data\CustomerAddressInterface|null
     */
    public function getBillingAddress(): ?CustomerAddressInterface;

    /**
     * SetBillingAddress
     *
     * @param \Emartech\Emarsys\Api\Data\CustomerAddressInterface|array|null $billingAddress
     *
     * @return \Emartech\Emarsys\Api\Data\CustomerInterface
     */
    public function setBillingAddress($billingAddress = null): CustomerInterface;

    /**
     * GetShippingAddress
     *
     * @return \Emartech\Emarsys\Api\Data\CustomerAddressInterface|null
     */
    public function getShippingAddress(): ?CustomerAddressInterface;

    /**
     * SetShippingAddress
     *
     * @param \Emartech\Emarsys\Api\Data\CustomerAddressInterface|array|null $shippingAddress
     *
     * @return \Emartech\Emarsys\Api\Data\CustomerInterface
     */
    public function setShippingAddress($shippingAddress = null): CustomerInterface;

    /**
     * GetAcceptsMarketing
     *
     * @return int|null
     */
    public function getAcceptsMarketing(): ?int;

    /**
     * SetAcceptsMarketing
     *
     * @param int|null $acceptsMarketing
     *
     * @return \Emartech\Emarsys\Api\Data\CustomerInterface
     */
    public function setAcceptsMarketing(?int $acceptsMarketing = null): CustomerInterface;

    /**
     * GetCreatedAt
     *
     * @return string|null
     */
    public function getCreatedAt(): ?string;

    /**
     * SetCreatedAt
     *
     * @param string|null $createdAt
     *
     * @return \Emartech\Emarsys\Api\Data\CustomerInterface
     */
    public function setCreatedAt(?string $createdAt = null): CustomerInterface;

    /**
     * GetUpdatedAt
     *
     * @return string|null
     */
    public function getUpdatedAt(): ?string;

    /**
     * SetUpdatedAt
     *
     * @param string|null $updatedAt
     *
     * @return \Emartech\Emarsys\Api\Data\CustomerInterface
     */
    public function setUpdatedAt(?string $updatedAt = null): CustomerInterface;

    /**
     * SetExtraFields
     *
     * @param \Emartech\Emarsys\Api\Data\ExtraFieldsInterface[]|null $extraFields
     *
     * @return \Emartech\Emarsys\Api\Data\CustomerInterface
     */
    public function setExtraFields(?array $extraFields = null): CustomerInterface;
}

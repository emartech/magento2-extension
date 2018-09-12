<?php

namespace Emartech\Emarsys\Api\Data;

/**
 * Interface CustomerInterface
 * @package Emartech\Emarsys\Api\Data
 */
interface CustomerInterface
{
    const ID_KEY                = 'id';
    const EMAIL_KEY             = 'email';
    const WEBSITE_ID_KEY        = 'website_id';
    const GROUP_ID_KEY          = 'group_id';
    const STORE_ID_KEY          = 'store_id';
    const IS_ACTIVE_KEY         = 'is_active';
    const PREFIX_KEY            = 'prefix';
    const FIRSTNAME_KEY         = 'firstname';
    const MIDDLENAME_KEY        = 'middlename';
    const LASTNAME_KEY          = 'lastname';
    const SUFFIX_KEY            = 'suffix';
    const DOB_KEY               = 'dob';
    const TAXVAT_KEY            = 'taxvat';
    const GENDER_KEY            = 'gender';
    const BILLING_ADDRESS_KEY   = 'billing_address';
    const SHIPPING_ADDRESS_KEY  = 'shipping_address';
    const ACCEPTS_MARKETING_KEY = 'accepts_marketing';
    const CREATED_AT_KEY        = 'created_at';
    const UPDATED_AT_KEY        = 'updated_at';

    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id): CustomerInterface;

    /**
     * @return string
     */
    public function getEmail(): string;

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email): CustomerInterface;

    /**
     * @return int
     */
    public function getWebsiteId(): int;

    /**
     * @param int $websiteId
     *
     * @return $this
     */
    public function setWebsiteId($websiteId): CustomerInterface;

    /**
     * @return int
     */
    public function getGroupId(): int;

    /**
     * @param int $groupId
     *
     * @return $this
     */
    public function setGroupId($groupId): CustomerInterface;

    /**
     * @return int
     */
    public function getStoreId(): int;

    /**
     * @param int $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId): CustomerInterface;

    /**
     * @return int
     */
    public function getIsActive(): int;

    /**
     * @param int $isActive
     *
     * @return $this
     */
    public function setIsActive($isActive): CustomerInterface;

    /**
     * @return string
     */
    public function getPrefix(): string;

    /**
     * @param string $prefix
     *
     * @return $this
     */
    public function setPrefix($prefix): CustomerInterface;

    /**
     * @return string
     */
    public function getFirstname(): string;

    /**
     * @param string $firstName
     *
     * @return $this
     */
    public function setFirstname($firstName): CustomerInterface;

    /**
     * @return string
     */
    public function getMiddlename(): string;

    /**
     * @param string $middleName
     *
     * @return $this
     */
    public function setMiddlename($middleName): CustomerInterface;

    /**
     * @return string
     */
    public function getLastname(): string;

    /**
     * @param string $lastName
     *
     * @return $this
     */
    public function setLastname($lastName): CustomerInterface;

    /**
     * @return string
     */
    public function getSuffix(): string;

    /**
     * @param string $suffix
     *
     * @return $this
     */
    public function setSuffix($suffix): CustomerInterface;

    /**
     * @return string
     */
    public function getDob(): string;

    /**
     * @param string $dob
     *
     * @return $this
     */
    public function setDob($dob): CustomerInterface;

    /**
     * @return string
     */
    public function getTaxvat(): string;

    /**
     * @param string $taxVat
     *
     * @return $this
     */
    public function setTaxvat($taxVat): CustomerInterface;

    /**
     * @return int
     */
    public function getGender(): int;

    /**
     * @param int $gender
     *
     * @return $this
     */
    public function setGender($gender): CustomerInterface;

    /**
     * @return \Emartech\Emarsys\Api\Data\CustomerAddressInterface
     */
    public function getBillingAddress(): CustomerAddressInterface;

    /**
     * @param \Emartech\Emarsys\Api\Data\CustomerAddressInterface $billingAddress
     *
     * @return $this
     */
    public function setBillingAddress($billingAddress): CustomerInterface;

    /**
     * @return \Emartech\Emarsys\Api\Data\CustomerAddressInterface
     */
    public function getShippingAddress(): CustomerAddressInterface;

    /**
     * @param \Emartech\Emarsys\Api\Data\CustomerAddressInterface $shippingAddress
     *
     * @return $this
     */
    public function setShippingAddress($shippingAddress): CustomerInterface;

    /**
     * @return int
     */
    public function getAcceptsMarketing(): int;

    /**
     * @param int $acceptsMarketing
     *
     * @return $this
     */
    public function setAcceptsMarketing($acceptsMarketing): CustomerInterface;

    /**
     * @return string
     */
    public function getCreatedAt(): string;

    /**
     * @param string $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt): CustomerInterface;

    /**
     * @return string
     */
    public function getUpdatedAt(): string;

    /**
     * @param string $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt($updatedAt): CustomerInterface;
}

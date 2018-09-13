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
    public function getId();

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id);

    /**
     * @return string|null
     */
    public function getEmail();

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email);

    /**
     * @return int|null
     */
    public function getWebsiteId();

    /**
     * @param int $websiteId
     *
     * @return $this
     */
    public function setWebsiteId($websiteId);

    /**
     * @return int
     */
    public function getGroupId();

    /**
     * @param int $groupId
     *
     * @return $this
     */
    public function setGroupId($groupId);

    /**
     * @return int|null
     */
    public function getStoreId();

    /**
     * @param int $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId);

    /**
     * @return int
     */
    public function getIsActive();

    /**
     * @param int $isActive
     *
     * @return $this
     */
    public function setIsActive($isActive);

    /**
     * @return string|null
     */
    public function getPrefix();

    /**
     * @param string $prefix
     *
     * @return $this
     */
    public function setPrefix($prefix);

    /**
     * @return string|null
     */
    public function getFirstname();

    /**
     * @param string $firstName
     *
     * @return $this
     */
    public function setFirstname($firstName);

    /**
     * @return string|null
     */
    public function getMiddlename();

    /**
     * @param string $middleName
     *
     * @return $this
     */
    public function setMiddlename($middleName);

    /**
     * @return string|null
     */
    public function getLastname();

    /**
     * @param string $lastName
     *
     * @return $this
     */
    public function setLastname($lastName);

    /**
     * @return string|null
     */
    public function getSuffix();

    /**
     * @param string $suffix
     *
     * @return $this
     */
    public function setSuffix($suffix);

    /**
     * @return string|null
     */
    public function getDob();

    /**
     * @param string $dob
     *
     * @return $this
     */
    public function setDob($dob);

    /**
     * @return string|null
     */
    public function getTaxvat();

    /**
     * @param string $taxVat
     *
     * @return $this
     */
    public function setTaxvat($taxVat);

    /**
     * @return int|null
     */
    public function getGender();

    /**
     * @param int $gender
     *
     * @return $this
     */
    public function setGender($gender);

    /**
     * @return \Emartech\Emarsys\Api\Data\CustomerAddressInterface
     */
    public function getBillingAddress();

    /**
     * @param \Emartech\Emarsys\Api\Data\CustomerAddressInterface $billingAddress
     *
     * @return $this
     */
    public function setBillingAddress($billingAddress);

    /**
     * @return \Emartech\Emarsys\Api\Data\CustomerAddressInterface
     */
    public function getShippingAddress();

    /**
     * @param \Emartech\Emarsys\Api\Data\CustomerAddressInterface $shippingAddress
     *
     * @return $this
     */
    public function setShippingAddress($shippingAddress);

    /**
     * @return int|null
     */
    public function getAcceptsMarketing();

    /**
     * @param int|null $acceptsMarketing
     *
     * @return $this
     */
    public function setAcceptsMarketing($acceptsMarketing);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @param string $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt($updatedAt);
}

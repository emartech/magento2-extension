<?php

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\CustomerAddressInterface;
use Magento\Framework\DataObject;

use Emartech\Emarsys\Api\Data\CustomerInterface;

/**
 * Class Customer
 * @package Emartech\Emarsys\Model\Data
 */
class Customer extends DataObject implements CustomerInterface
{
    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->getData(self::ID_KEY);
    }

    /**
     * @return int
     */
    public function getAcceptsMarketing(): int
    {
        return $this->getData(self::ACCEPTS_MARKETING_KEY);
    }

    /**
     * @return \Emartech\Emarsys\Api\Data\CustomerAddressInterface
     */
    public function getBillingAddress(): CustomerAddressInterface
    {
        return $this->getData(self::BILLING_ADDRESS_KEY);
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->getData(self::CREATED_AT_KEY);
    }

    /**
     * @return string
     */
    public function getDob(): string
    {
        return $this->getData(self::DOB_KEY);
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->getData(self::EMAIL_KEY);
    }

    /**
     * @return string
     */
    public function getFirstname(): string
    {
        return $this->getData(self::FIRSTNAME_KEY);
    }

    /**
     * @return int
     */
    public function getGender(): int
    {
        return $this->getData(self::GENDER_KEY);
    }

    /**
     * @return int
     */
    public function getGroupId(): int
    {
        return $this->getData(self::GROUP_ID_KEY);
    }

    /**
     * @return int
     */
    public function getIsActive(): int
    {
        return $this->getData(self::IS_ACTIVE_KEY);
    }

    /**
     * @return string
     */
    public function getLastname(): string
    {
        return $this->getData(self::LASTNAME_KEY);
    }

    /**
     * @return string
     */
    public function getMiddlename(): string
    {
        return $this->getData(self::MIDDLENAME_KEY);
    }

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->getData(self::PREFIX_KEY);
    }

    /**
     * @return CustomerAddressInterface
     */
    public function getShippingAddress(): CustomerAddressInterface
    {
        return $this->getData(self::SHIPPING_ADDRESS_KEY);
    }

    /**
     * @return int
     */
    public function getStoreId(): int
    {
        return $this->getData(self::STORE_ID_KEY);
    }

    /**
     * @return string
     */
    public function getSuffix(): string
    {
        return $this->getData(self::SUFFIX_KEY);
    }

    /**
     * @return string
     */
    public function getTaxvat(): string
    {
        return $this->getData(self::TAXVAT_KEY);
    }

    /**
     * @return string
     */
    public function getUpdatedAt(): string
    {
        return $this->getData(self::UPDATED_AT_KEY);
    }

    /**
     * @return int
     */
    public function getWebsiteId(): int
    {
        return $this->getData(self::WEBSITE_ID_KEY);
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id): CustomerInterface
    {
        $this->setData(self::ID_KEY, $id);
        return $this;
    }

    /**
     * @param int $acceptsMarketing
     *
     * @return $this
     */
    public function setAcceptsMarketing($acceptsMarketing): CustomerInterface
    {
        $this->setData(self::ACCEPTS_MARKETING_KEY, $acceptsMarketing);
        return $this;
    }

    /**
     * @param \Emartech\Emarsys\Api\Data\CustomerAddressInterface $billingAddress
     *
     * @return $this
     */
    public function setBillingAddress($billingAddress): CustomerInterface
    {
        $this->setData(self::BILLING_ADDRESS_KEY, $billingAddress);
        return $this;
    }

    /**
     * @param string $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt): CustomerInterface
    {
        $this->setData(self::CREATED_AT_KEY, $createdAt);
        return $this;
    }

    /**
     * @param string $dob
     *
     * @return $this
     */
    public function setDob($dob): CustomerInterface
    {
        $this->setData(self::DOB_KEY, $dob);
        return $this;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email): CustomerInterface
    {
        $this->setData(self::EMAIL_KEY, $email);
        return $this;
    }

    /**
     * @param string $firstName
     *
     * @return $this
     */
    public function setFirstname($firstName): CustomerInterface
    {
        $this->setData(self::FIRSTNAME_KEY, $firstName);
        return $this;
    }

    /**
     * @param int $gender
     *
     * @return $this
     */
    public function setGender($gender): CustomerInterface
    {
        $this->setData(self::GENDER_KEY, $gender);
        return $this;
    }

    /**
     * @param int $groupId
     *
     * @return $this
     */
    public function setGroupId($groupId): CustomerInterface
    {
        $this->setData(self::GROUP_ID_KEY, $groupId);
        return $this;
    }

    /**
     * @param int $isActive
     *
     * @return $this
     */
    public function setIsActive($isActive): CustomerInterface
    {
        $this->setData(self::IS_ACTIVE_KEY, $isActive);
        return $this;
    }

    /**
     * @param string $lastName
     *
     * @return $this
     */
    public function setLastname($lastName): CustomerInterface
    {
        $this->setData(self::LASTNAME_KEY, $lastName);
        return $this;
    }

    /**
     * @param string $middleName
     *
     * @return $this
     */
    public function setMiddlename($middleName): CustomerInterface
    {
        $this->setData(self::MIDDLENAME_KEY, $middleName);
        return $this;
    }

    /**
     * @param string $prefix
     *
     * @return $this
     */
    public function setPrefix($prefix): CustomerInterface
    {
        $this->setData(self::PREFIX_KEY, $prefix);
        return $this;
    }

    /**
     * @param \Emartech\Emarsys\Api\Data\CustomerAddressInterface $shippingAddress
     *
     * @return $this
     */
    public function setShippingAddress($shippingAddress): CustomerInterface
    {
        $this->setData(self::SHIPPING_ADDRESS_KEY, $shippingAddress);
        return $this;
    }

    /**
     * @param int $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId): CustomerInterface
    {
        $this->setData(self::STORE_ID_KEY, $storeId);
        return $this;
    }

    /**
     * @param string $suffix
     *
     * @return $this
     */
    public function setSuffix($suffix): CustomerInterface
    {
        $this->setData(self::SUFFIX_KEY, $suffix);
        return $this;
    }

    /**
     * @param string $taxVat
     *
     * @return $this
     */
    public function setTaxvat($taxVat): CustomerInterface
    {
        $this->setData(self::TAXVAT_KEY, $taxVat);
        return $this;
    }

    /**
     * @param string $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt($updatedAt): CustomerInterface
    {
        $this->setData(self::UPDATED_AT_KEY, $updatedAt);
        return $this;
    }

    /**
     * @param int $websiteId
     *
     * @return $this
     */
    public function setWebsiteId($websiteId): CustomerInterface
    {
        $this->setData(self::WEBSITE_ID_KEY, $websiteId);
        return $this;
    }
}

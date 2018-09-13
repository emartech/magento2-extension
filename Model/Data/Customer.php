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
    public function getId()
    {
        return $this->getData(self::ID_KEY);
    }

    /**
     * @return int
     */
    public function getAcceptsMarketing()
    {
        return $this->getData(self::ACCEPTS_MARKETING_KEY);
    }

    /**
     * @return \Emartech\Emarsys\Api\Data\CustomerAddressInterface
     */
    public function getBillingAddress()
    {
        return $this->getData(self::BILLING_ADDRESS_KEY);
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT_KEY);
    }

    /**
     * @return string|null
     */
    public function getDob()
    {
        return $this->getData(self::DOB_KEY);
    }

    /**
     * @return string|null
     */
    public function getEmail()
    {
        return $this->getData(self::EMAIL_KEY);
    }

    /**
     * @return string|null
     */
    public function getFirstname()
    {
        return $this->getData(self::FIRSTNAME_KEY);
    }

    /**
     * @return int|null
     */
    public function getGender()
    {
        return $this->getData(self::GENDER_KEY);
    }

    /**
     * @return int
     */
    public function getGroupId()
    {
        return $this->getData(self::GROUP_ID_KEY);
    }

    /**
     * @return int
     */
    public function getIsActive()
    {
        return $this->getData(self::IS_ACTIVE_KEY);
    }

    /**
     * @return string|null
     */
    public function getLastname()
    {
        return $this->getData(self::LASTNAME_KEY);
    }

    /**
     * @return string|null
     */
    public function getMiddlename()
    {
        return $this->getData(self::MIDDLENAME_KEY);
    }

    /**
     * @return string|null
     */
    public function getPrefix()
    {
        return $this->getData(self::PREFIX_KEY);
    }

    /**
     * @return CustomerAddressInterface
     */
    public function getShippingAddress()
    {
        return $this->getData(self::SHIPPING_ADDRESS_KEY);
    }

    /**
     * @return int|null
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID_KEY);
    }

    /**
     * @return string|null
     */
    public function getSuffix()
    {
        return $this->getData(self::SUFFIX_KEY);
    }

    /**
     * @return string|null
     */
    public function getTaxvat()
    {
        return $this->getData(self::TAXVAT_KEY);
    }

    /**
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT_KEY);
    }

    /**
     * @return int|null
     */
    public function getWebsiteId()
    {
        return $this->getData(self::WEBSITE_ID_KEY);
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->setData(self::ID_KEY, $id);
        return $this;
    }

    /**
     * @param int|null $acceptsMarketing
     *
     * @return $this
     */
    public function setAcceptsMarketing($acceptsMarketing)
    {
        $this->setData(self::ACCEPTS_MARKETING_KEY, $acceptsMarketing);
        return $this;
    }

    /**
     * @param \Emartech\Emarsys\Api\Data\CustomerAddressInterface $billingAddress
     *
     * @return $this
     */
    public function setBillingAddress($billingAddress)
    {
        $this->setData(self::BILLING_ADDRESS_KEY, $billingAddress);
        return $this;
    }

    /**
     * @param string $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->setData(self::CREATED_AT_KEY, $createdAt);
        return $this;
    }

    /**
     * @param string $dob
     *
     * @return $this
     */
    public function setDob($dob)
    {
        $this->setData(self::DOB_KEY, $dob);
        return $this;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->setData(self::EMAIL_KEY, $email);
        return $this;
    }

    /**
     * @param string $firstName
     *
     * @return $this
     */
    public function setFirstname($firstName)
    {
        $this->setData(self::FIRSTNAME_KEY, $firstName);
        return $this;
    }

    /**
     * @param int $gender
     *
     * @return $this
     */
    public function setGender($gender)
    {
        $this->setData(self::GENDER_KEY, $gender);
        return $this;
    }

    /**
     * @param int $groupId
     *
     * @return $this
     */
    public function setGroupId($groupId)
    {
        $this->setData(self::GROUP_ID_KEY, $groupId);
        return $this;
    }

    /**
     * @param int $isActive
     *
     * @return $this
     */
    public function setIsActive($isActive)
    {
        $this->setData(self::IS_ACTIVE_KEY, $isActive);
        return $this;
    }

    /**
     * @param string $lastName
     *
     * @return $this
     */
    public function setLastname($lastName)
    {
        $this->setData(self::LASTNAME_KEY, $lastName);
        return $this;
    }

    /**
     * @param string $middleName
     *
     * @return $this
     */
    public function setMiddlename($middleName)
    {
        $this->setData(self::MIDDLENAME_KEY, $middleName);
        return $this;
    }

    /**
     * @param string $prefix
     *
     * @return $this
     */
    public function setPrefix($prefix)
    {
        $this->setData(self::PREFIX_KEY, $prefix);
        return $this;
    }

    /**
     * @param \Emartech\Emarsys\Api\Data\CustomerAddressInterface $shippingAddress
     *
     * @return $this
     */
    public function setShippingAddress($shippingAddress)
    {
        $this->setData(self::SHIPPING_ADDRESS_KEY, $shippingAddress);
        return $this;
    }

    /**
     * @param int $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->setData(self::STORE_ID_KEY, $storeId);
        return $this;
    }

    /**
     * @param string $suffix
     *
     * @return $this
     */
    public function setSuffix($suffix)
    {
        $this->setData(self::SUFFIX_KEY, $suffix);
        return $this;
    }

    /**
     * @param string $taxVat
     *
     * @return $this
     */
    public function setTaxvat($taxVat)
    {
        $this->setData(self::TAXVAT_KEY, $taxVat);
        return $this;
    }

    /**
     * @param string $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->setData(self::UPDATED_AT_KEY, $updatedAt);
        return $this;
    }

    /**
     * @param int $websiteId
     *
     * @return $this
     */
    public function setWebsiteId($websiteId)
    {
        $this->setData(self::WEBSITE_ID_KEY, $websiteId);
        return $this;
    }
}

<?php

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\CustomerAddressInterface;
use Emartech\Emarsys\Api\Data\CustomerInterface;
use Emartech\Emarsys\Api\Data\ExtraFieldsInterface;
use Magento\Framework\DataObject;

class Customer extends DataObject implements CustomerInterface
{
    /**
     * GetId
     *
     * @return int
     */
    public function getId(): int
    {
        return (int) $this->getData(self::ID_KEY);
    }

    /**
     * GetAcceptsMarketing
     *
     * @return int|null
     */
    public function getAcceptsMarketing(): ?int
    {
        return $this->getData(self::ACCEPTS_MARKETING_KEY);
    }

    /**
     * GetBillingAddress
     *
     * @return CustomerAddressInterface|null
     */
    public function getBillingAddress(): ?CustomerAddressInterface
    {
        return $this->getData(self::BILLING_ADDRESS_KEY);
    }

    /**
     * GetCreatedAt
     *
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->getData(self::CREATED_AT_KEY);
    }

    /**
     * GetDob
     *
     * @return string|null
     */
    public function getDob(): ?string
    {
        return $this->getData(self::DOB_KEY);
    }

    /**
     * GetEmail
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->getData(self::EMAIL_KEY);
    }

    /**
     * GetFirstname
     *
     * @return string|null
     */
    public function getFirstname(): ?string
    {
        return $this->getData(self::FIRSTNAME_KEY);
    }

    /**
     * GetGender
     *
     * @return int|null
     */
    public function getGender(): ?int
    {
        return $this->getData(self::GENDER_KEY);
    }

    /**
     * GetGroupId
     *
     * @return int|null
     */
    public function getGroupId(): ?int
    {
        return $this->getData(self::GROUP_ID_KEY);
    }

    /**
     * GetIsActive
     *
     * @return int|null
     */
    public function getIsActive(): ?int
    {
        return $this->getData(self::IS_ACTIVE_KEY);
    }

    /**
     * GetLastname
     *
     * @return string|null
     */
    public function getLastname(): ?string
    {
        return $this->getData(self::LASTNAME_KEY);
    }

    /**
     * GetMiddlename
     *
     * @return string|null
     */
    public function getMiddlename(): ?string
    {
        return $this->getData(self::MIDDLENAME_KEY);
    }

    /**
     * GetPrefix
     *
     * @return string|null
     */
    public function getPrefix(): ?string
    {
        return $this->getData(self::PREFIX_KEY);
    }

    /**
     * GetShippingAddress
     *
     * @return CustomerAddressInterface|null
     */
    public function getShippingAddress(): ?CustomerAddressInterface
    {
        return $this->getData(self::SHIPPING_ADDRESS_KEY);
    }

    /**
     * GetStoreId
     *
     * @return int|null
     */
    public function getStoreId(): ?int
    {
        return $this->getData(self::STORE_ID_KEY);
    }

    /**
     * GetSuffix
     *
     * @return string|null
     */
    public function getSuffix(): ?string
    {
        return $this->getData(self::SUFFIX_KEY);
    }

    /**
     * GetTaxvat
     *
     * @return string|null
     */
    public function getTaxvat(): ?string
    {
        return $this->getData(self::TAXVAT_KEY);
    }

    /**
     * GetUpdatedAt
     *
     * @return string|null
     */
    public function getUpdatedAt(): ?string
    {
        return $this->getData(self::UPDATED_AT_KEY);
    }

    /**
     * GetWebsiteId
     *
     * @return int|null
     */
    public function getWebsiteId(): ?int
    {
        return $this->getData(self::WEBSITE_ID_KEY);
    }

    /**
     * GetExtraFields
     *
     * @return ExtraFieldsInterface[]|null
     */
    public function getExtraFields(): ?array
    {
        return $this->getData(self::EXTRA_FIELDS);
    }

    /**
     * SetId
     *
     * @param int $id
     *
     * @return CustomerInterface
     */
    public function setId(int $id): CustomerInterface
    {
        $this->setData(self::ID_KEY, $id);

        return $this;
    }

    /**
     * SetAcceptsMarketing
     *
     * @param int|null $acceptsMarketing
     *
     * @return CustomerInterface
     */
    public function setAcceptsMarketing(?int $acceptsMarketing = null): CustomerInterface
    {
        $this->setData(self::ACCEPTS_MARKETING_KEY, $acceptsMarketing);

        return $this;
    }

    /**
     * SetBillingAddress
     *
     * @param CustomerAddressInterface|array|null $billingAddress
     *
     * @return CustomerInterface
     */
    public function setBillingAddress($billingAddress = null): CustomerInterface
    {
        $this->setData(self::BILLING_ADDRESS_KEY, $billingAddress);

        return $this;
    }

    /**
     * SetCreatedAt
     *
     * @param string|null $createdAt
     *
     * @return CustomerInterface
     */
    public function setCreatedAt(?string $createdAt = null): CustomerInterface
    {
        $this->setData(self::CREATED_AT_KEY, $createdAt);

        return $this;
    }

    /**
     * SetDob
     *
     * @param string|null $dob
     *
     * @return $this
     */
    public function setDob(?string $dob = null): CustomerInterface
    {
        $this->setData(self::DOB_KEY, $dob);

        return $this;
    }

    /**
     * SetEmail
     *
     * @param string|null $email
     *
     * @return CustomerInterface
     */
    public function setEmail(?string $email = null): CustomerInterface
    {
        $this->setData(self::EMAIL_KEY, $email);

        return $this;
    }

    /**
     * SetFirstname
     *
     * @param string|null $firstName
     *
     * @return CustomerInterface
     */
    public function setFirstname(?string $firstName = null): CustomerInterface
    {
        $this->setData(self::FIRSTNAME_KEY, $firstName);

        return $this;
    }

    /**
     * SetGender
     *
     * @param int|null $gender
     *
     * @return CustomerInterface
     */
    public function setGender(?int $gender = null): CustomerInterface
    {
        $this->setData(self::GENDER_KEY, $gender);

        return $this;
    }

    /**
     * SetGroupId
     *
     * @param int|null $groupId
     *
     * @return CustomerInterface
     */
    public function setGroupId(?int $groupId = null): CustomerInterface
    {
        $this->setData(self::GROUP_ID_KEY, $groupId);

        return $this;
    }

    /**
     * SetIsActive
     *
     * @param int|null $isActive
     *
     * @return CustomerInterface
     */
    public function setIsActive(?int $isActive = null): CustomerInterface
    {
        $this->setData(self::IS_ACTIVE_KEY, $isActive);

        return $this;
    }

    /**
     * SetLastname
     *
     * @param string|null $lastName
     *
     * @return CustomerInterface
     */
    public function setLastname(?string $lastName = null): CustomerInterface
    {
        $this->setData(self::LASTNAME_KEY, $lastName);

        return $this;
    }

    /**
     * SetMiddlename
     *
     * @param string|null $middleName
     *
     * @return CustomerInterface
     */
    public function setMiddlename(?string $middleName = null): CustomerInterface
    {
        $this->setData(self::MIDDLENAME_KEY, $middleName);

        return $this;
    }

    /**
     * SetPrefix
     *
     * @param string|null $prefix
     *
     * @return CustomerInterface
     */
    public function setPrefix(?string $prefix = null): CustomerInterface
    {
        $this->setData(self::PREFIX_KEY, $prefix);

        return $this;
    }

    /**
     * SetShippingAddress
     *
     * @param CustomerAddressInterface|array|null $shippingAddress
     *
     * @return $this
     */
    public function setShippingAddress($shippingAddress = null): CustomerInterface
    {
        $this->setData(self::SHIPPING_ADDRESS_KEY, $shippingAddress);

        return $this;
    }

    /**
     * SetStoreId
     *
     * @param int|null $storeId
     *
     * @return CustomerInterface
     */
    public function setStoreId(?int $storeId = null): CustomerInterface
    {
        $this->setData(self::STORE_ID_KEY, $storeId);

        return $this;
    }

    /**
     * SetSuffix
     *
     * @param string|null $suffix
     *
     * @return CustomerInterface
     */
    public function setSuffix(?string $suffix = null): CustomerInterface
    {
        $this->setData(self::SUFFIX_KEY, $suffix);

        return $this;
    }

    /**
     * SetTaxvat
     *
     * @param string|null $taxVat
     *
     * @return CustomerInterface
     */
    public function setTaxvat(?string $taxVat = null): CustomerInterface
    {
        $this->setData(self::TAXVAT_KEY, $taxVat);

        return $this;
    }

    /**
     * SetUpdatedAt
     *
     * @param string|null $updatedAt
     *
     * @return CustomerInterface
     */
    public function setUpdatedAt(?string $updatedAt = null): CustomerInterface
    {
        $this->setData(self::UPDATED_AT_KEY, $updatedAt);

        return $this;
    }

    /**
     * SetWebsiteId
     *
     * @param int|null $websiteId
     *
     * @return CustomerInterface
     */
    public function setWebsiteId(?int $websiteId = null): CustomerInterface
    {
        $this->setData(self::WEBSITE_ID_KEY, $websiteId);

        return $this;
    }

    /**
     * SetExtraFields
     *
     * @param ExtraFieldsInterface[]|null $extraFields
     *
     * @return $this
     */
    public function setExtraFields(?array $extraFields = null): CustomerInterface
    {
        $this->setData(self::EXTRA_FIELDS, $extraFields);

        return $this;
    }
}

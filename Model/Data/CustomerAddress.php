<?php

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\CustomerAddressInterface;
use Emartech\Emarsys\Api\Data\ExtraFieldsInterface;
use Magento\Framework\DataObject;

class CustomerAddress extends DataObject implements CustomerAddressInterface
{
    /**
     * GetCity
     *
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->getData(self::CITY_KEY);
    }

    /**
     * GetCompany
     *
     * @return string|null
     */
    public function getCompany(): ?string
    {
        return $this->getData(self::COMPANY_KEY);
    }

    /**
     * GetCountryId
     *
     * @return string|null
     */
    public function getCountryId(): ?string
    {
        return $this->getData(self::COUNTRY_ID_KEY);
    }

    /**
     * GetFax
     *
     * @return string|null
     */
    public function getFax(): ?string
    {
        return $this->getData(self::FAX_KEY);
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
     * GetPostcode
     *
     * @return string|null
     */
    public function getPostcode(): ?string
    {
        return $this->getData(self::POSTCODE_KEY);
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
     * GetRegion
     *
     * @return string|null
     */
    public function getRegion(): ?string
    {
        return $this->getData(self::REGION_KEY);
    }

    /**
     * GetStreet
     *
     * @return string|null
     */
    public function getStreet(): ?string
    {
        return $this->getData(self::STREET_KEY);
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
     * GetTelephone
     *
     * @return string|null
     */
    public function getTelephone(): ?string
    {
        return $this->getData(self::TELEPHONE_KEY);
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
     * SetCity
     *
     * @param string|null $city
     *
     * @return CustomerAddressInterface
     */
    public function setCity(?string $city = null): CustomerAddressInterface
    {
        $this->setData(self::CITY_KEY, $city);

        return $this;
    }

    /**
     * SetCompany
     *
     * @param string|null $company
     *
     * @return CustomerAddressInterface
     */
    public function setCompany(?string $company = null): CustomerAddressInterface
    {
        $this->setData(self::COMPANY_KEY, $company);

        return $this;
    }

    /**
     * SetCountryId
     *
     * @param string|null $countryId
     *
     * @return CustomerAddressInterface
     */
    public function setCountryId(?string $countryId = null): CustomerAddressInterface
    {
        $this->setData(self::COUNTRY_ID_KEY, $countryId);

        return $this;
    }

    /**
     * SetFax
     *
     * @param string|null $fax
     *
     * @return CustomerAddressInterface
     */
    public function setFax(?string $fax = null): CustomerAddressInterface
    {
        $this->setData(self::FAX_KEY, $fax);

        return $this;
    }

    /**
     * SetFirstname
     *
     * @param string|null $firstName
     *
     * @return CustomerAddressInterface
     */
    public function setFirstname(?string $firstName = null): CustomerAddressInterface
    {
        $this->setData(self::FIRSTNAME_KEY, $firstName);

        return $this;
    }

    /**
     * SetLastname
     *
     * @param string|null $lastName
     *
     * @return CustomerAddressInterface
     */
    public function setLastname(?string $lastName = null): CustomerAddressInterface
    {
        $this->setData(self::LASTNAME_KEY, $lastName);

        return $this;
    }

    /**
     * SetMiddlename
     *
     * @param string|null $middleName
     *
     * @return CustomerAddressInterface
     */
    public function setMiddlename(?string $middleName = null): CustomerAddressInterface
    {
        $this->setData(self::MIDDLENAME_KEY, $middleName);

        return $this;
    }

    /**
     * SetPostcode
     *
     * @param string $postCode
     *
     * @return CustomerAddressInterface
     */
    public function setPostcode(?string $postCode = null): CustomerAddressInterface
    {
        $this->setData(self::POSTCODE_KEY, $postCode);

        return $this;
    }

    /**
     * SetPrefix
     *
     * @param string|null $prefix
     *
     * @return CustomerAddressInterface
     */
    public function setPrefix(?string $prefix = null): CustomerAddressInterface
    {
        $this->setData(self::PREFIX_KEY, $prefix);

        return $this;
    }

    /**
     * SetRegion
     *
     * @param string|null $region
     *
     * @return CustomerAddressInterface
     */
    public function setRegion(?string $region = null): CustomerAddressInterface
    {
        $this->setData(self::REGION_KEY, $region);

        return $this;
    }

    /**
     * SetStreet
     *
     * @param string|null $street
     *
     * @return CustomerAddressInterface
     */
    public function setStreet(?string $street = null): CustomerAddressInterface
    {
        $this->setData(self::STREET_KEY, $street);

        return $this;
    }

    /**
     * SetSuffix
     *
     * @param string|null $suffix
     *
     * @return CustomerAddressInterface
     */
    public function setSuffix(?string $suffix = null): CustomerAddressInterface
    {
        $this->setData(self::SUFFIX_KEY, $suffix);

        return $this;
    }

    /**
     * SetTelephone
     *
     * @param string|null $telephone
     *
     * @return CustomerAddressInterface
     */
    public function setTelephone(?string $telephone = null): CustomerAddressInterface
    {
        $this->setData(self::TELEPHONE_KEY, $telephone);

        return $this;
    }

    /**
     * SetExtraFields
     *
     * @param ExtraFieldsInterface[]|null $extraFields
     *
     * @return CustomerAddressInterface
     */
    public function setExtraFields(?array $extraFields = null): CustomerAddressInterface
    {
        $this->setData(self::EXTRA_FIELDS, $extraFields);

        return $this;
    }
}

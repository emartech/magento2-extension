<?php

namespace Emartech\Emarsys\Model\Data;

use Magento\Framework\DataObject;

use Emartech\Emarsys\Api\Data\CustomerAddressInterface;

/**
 * Class CustomerAddress
 * @package Emartech\Emarsys\Model\Data
 */
class CustomerAddress extends DataObject implements CustomerAddressInterface
{
    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->getData(self::CITY_KEY);
    }

    /**
     * @return string
     */
    public function getCompany(): string
    {
        return $this->getData(self::COMPANY_KEY);
    }

    /**
     * @return string
     */
    public function getCountryId(): string
    {
        return $this->getData(self::COUNTRY_ID_KEY);
    }

    /**
     * @return string
     */
    public function getFax(): string
    {
        return $this->getData(self::FAX_KEY);
    }

    /**
     * @return string
     */
    public function getFirstname(): string
    {
        return $this->getData(self::FIRSTNAME_KEY);
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
    public function getPostcode(): string
    {
        return $this->getData(self::POSTCODE_KEY);
    }

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->getData(self::PREFIX_KEY);
    }

    /**
     * @return string
     */
    public function getRegion(): string
    {
        return $this->getData(self::REGION_KEY);
    }

    /**
     * @return string
     */
    public function getStreet(): string
    {
        return $this->getData(self::STREET_KEY);
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
    public function getTelephone(): string
    {
        return $this->getData(self::TELEPHONE_KEY);
    }

    /**
     * @param string $city
     *
     * @return $this
     */
    public function setCity($city): CustomerAddressInterface
    {
        $this->setData(self::CITY_KEY, $city);
        return $this;
    }

    /**
     * @param string $company
     *
     * @return $this
     */
    public function setCompany($company): CustomerAddressInterface
    {
        $this->setData(self::COMPANY_KEY, $company);
        return $this;
    }

    /**
     * @param string $countryId
     *
     * @return $this
     */
    public function setCountryId($countryId): CustomerAddressInterface
    {
        $this->setData(self::COUNTRY_ID_KEY, $countryId);
        return $this;
    }

    /**
     * @param string $fax
     *
     * @return $this
     */
    public function setFax($fax): CustomerAddressInterface
    {
        $this->setData(self::FAX_KEY, $fax);
        return $this;
    }

    /**
     * @param string $firstName
     *
     * @return $this
     */
    public function setFirstname($firstName): CustomerAddressInterface
    {
        $this->setData(self::FIRSTNAME_KEY, $firstName);
        return $this;
    }

    /**
     * @param string $lastName
     *
     * @return $this
     */
    public function setLastname($lastName): CustomerAddressInterface
    {
        $this->setData(self::LASTNAME_KEY, $lastName);
        return $this;
    }

    /**
     * @param string $middleName
     *
     * @return $this
     */
    public function setMiddlename($middleName): CustomerAddressInterface
    {
        $this->setData(self::MIDDLENAME_KEY, $middleName);
        return $this;
    }

    /**
     * @param string $postCode
     *
     * @return $this
     */
    public function setPostcode($postCode): CustomerAddressInterface
    {
        $this->setData(self::POSTCODE_KEY, $postCode);
        return $this;
    }

    /**
     * @param string $prefix
     *
     * @return $this
     */
    public function setPrefix($prefix): CustomerAddressInterface
    {
        $this->setData(self::PREFIX_KEY, $prefix);
        return $this;
    }

    /**
     * @param string $region
     *
     * @return $this
     */
    public function setRegion($region): CustomerAddressInterface
    {
        $this->setData(self::REGION_KEY, $region);
        return $this;
    }

    /**
     * @param string $street
     *
     * @return $this
     */
    public function setStreet($street): CustomerAddressInterface
    {
        $this->setData(self::STREET_KEY, $street);
        return $this;
    }

    /**
     * @param string $suffix
     *
     * @return $this
     */
    public function setSuffix($suffix): CustomerAddressInterface
    {
        $this->setData(self::SUFFIX_KEY, $suffix);
        return $this;
    }

    /**
     * @param string $telephone
     *
     * @return $this
     */
    public function setTelephone($telephone): CustomerAddressInterface
    {
        $this->setData(self::TELEPHONE_KEY, $telephone);
        return $this;
    }
}

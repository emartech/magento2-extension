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
    public function getCity()
    {
        return $this->getData(self::CITY_KEY);
    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->getData(self::COMPANY_KEY);
    }

    /**
     * @return string
     */
    public function getCountryId()
    {
        return $this->getData(self::COUNTRY_ID_KEY);
    }

    /**
     * @return string
     */
    public function getFax()
    {
        return $this->getData(self::FAX_KEY);
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->getData(self::FIRSTNAME_KEY);
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->getData(self::LASTNAME_KEY);
    }

    /**
     * @return string
     */
    public function getMiddlename()
    {
        return $this->getData(self::MIDDLENAME_KEY);
    }

    /**
     * @return string
     */
    public function getPostcode()
    {
        return $this->getData(self::POSTCODE_KEY);
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->getData(self::PREFIX_KEY);
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        return $this->getData(self::REGION_KEY);
    }

    /**
     * @return string
     */
    public function getStreet()
    {
        return $this->getData(self::STREET_KEY);
    }

    /**
     * @return string
     */
    public function getSuffix()
    {
        return $this->getData(self::SUFFIX_KEY);
    }

    /**
     * @return string
     */
    public function getTelephone()
    {
        return $this->getData(self::TELEPHONE_KEY);
    }

    /**
     * @param string $city
     *
     * @return $this
     */
    public function setCity($city)
    {
        $this->setData(self::CITY_KEY, $city);
        return $this;
    }

    /**
     * @param string $company
     *
     * @return $this
     */
    public function setCompany($company)
    {
        $this->setData(self::COMPANY_KEY, $company);
        return $this;
    }

    /**
     * @param string $countryId
     *
     * @return $this
     */
    public function setCountryId($countryId)
    {
        $this->setData(self::COUNTRY_ID_KEY, $countryId);
        return $this;
    }

    /**
     * @param string $fax
     *
     * @return $this
     */
    public function setFax($fax)
    {
        $this->setData(self::FAX_KEY, $fax);
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
     * @param string $postCode
     *
     * @return $this
     */
    public function setPostcode($postCode)
    {
        $this->setData(self::POSTCODE_KEY, $postCode);
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
     * @param string $region
     *
     * @return $this
     */
    public function setRegion($region)
    {
        $this->setData(self::REGION_KEY, $region);
        return $this;
    }

    /**
     * @param string $street
     *
     * @return $this
     */
    public function setStreet($street)
    {
        $this->setData(self::STREET_KEY, $street);
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
     * @param string $telephone
     *
     * @return $this
     */
    public function setTelephone($telephone)
    {
        $this->setData(self::TELEPHONE_KEY, $telephone);
        return $this;
    }
}

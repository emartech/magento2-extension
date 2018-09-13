<?php

namespace Emartech\Emarsys\Api\Data;

/**
 * Interface CustomerAddressInterface
 * @package Emartech\Emarsys\Api\Data
 */
interface CustomerAddressInterface
{
    const PREFIX_KEY     = 'prefix';
    const FIRSTNAME_KEY  = 'firstname';
    const MIDDLENAME_KEY = 'middlename';
    const LASTNAME_KEY   = 'lastname';
    const SUFFIX_KEY     = 'suffix';
    const COMPANY_KEY    = 'company';
    const STREET_KEY     = 'street';
    const CITY_KEY       = 'city';
    const COUNTRY_ID_KEY = 'country_id';
    const REGION_KEY     = 'region';
    const POSTCODE_KEY   = 'postcode';
    const TELEPHONE_KEY  = 'telephone';
    const FAX_KEY        = 'fax';

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
    public function getCompany();

    /**
     * @param string $company
     *
     * @return $this
     */
    public function setCompany($company);

    /**
     * @return string|null
     */
    public function getStreet();

    /**
     * @param string $street
     *
     * @return $this
     */
    public function setStreet($street);

    /**
     * @return string|null
     */
    public function getCity();

    /**
     * @param string $city
     *
     * @return $this
     */
    public function setCity($city);

    /**
     * @return string|null
     */
    public function getCountryId();

    /**
     * @param string $countryId
     *
     * @return $this
     */
    public function setCountryId($countryId);

    /**
     * @return string|null
     */
    public function getRegion();

    /**
     * @param string $region
     *
     * @return $this
     */
    public function setRegion($region);

    /**
     * @return string|null
     */
    public function getPostcode();

    /**
     * @param string $postCode
     *
     * @return $this
     */
    public function setPostcode($postCode);

    /**
     * @return string|null
     */
    public function getTelephone();

    /**
     * @param string $telephone
     *
     * @return $this
     */
    public function setTelephone($telephone);

    /**
     * @return string|null
     */
    public function getFax();

    /**
     * @param string $fax
     *
     * @return $this
     */
    public function setFax($fax);
}

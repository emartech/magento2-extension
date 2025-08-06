<?php

namespace Emartech\Emarsys\Api\Data;

interface CustomerAddressInterface
{
    public const PREFIX_KEY     = 'prefix';
    public const FIRSTNAME_KEY  = 'firstname';
    public const MIDDLENAME_KEY = 'middlename';
    public const LASTNAME_KEY   = 'lastname';
    public const SUFFIX_KEY     = 'suffix';
    public const COMPANY_KEY    = 'company';
    public const STREET_KEY     = 'street';
    public const CITY_KEY       = 'city';
    public const COUNTRY_ID_KEY = 'country_id';
    public const REGION_KEY     = 'region';
    public const POSTCODE_KEY   = 'postcode';
    public const TELEPHONE_KEY  = 'telephone';
    public const FAX_KEY        = 'fax';
    public const EXTRA_FIELDS   = 'extra_fields';

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
     * @return \Emartech\Emarsys\Api\Data\CustomerAddressInterface
     */
    public function setPrefix(?string $prefix = null): CustomerAddressInterface;

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
     * @return \Emartech\Emarsys\Api\Data\CustomerAddressInterface
     */
    public function setFirstname(?string $firstName = null): CustomerAddressInterface;

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
     * @return \Emartech\Emarsys\Api\Data\CustomerAddressInterface
     */
    public function setMiddlename(?string $middleName = null): CustomerAddressInterface;

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
     * @return \Emartech\Emarsys\Api\Data\CustomerAddressInterface
     */
    public function setLastname(?string $lastName = null): CustomerAddressInterface;

    /**
     * GetSuffix
     *
     * @return string|null
     */
    public function getSuffix(): ?string;

    /**
     * GetExtraFields
     *
     * @return \Emartech\Emarsys\Api\Data\ExtraFieldsInterface[]|null
     */
    public function getExtraFields(): ?array;

    /**
     * SetSuffix
     *
     * @param string|null $suffix
     *
     * @return \Emartech\Emarsys\Api\Data\CustomerAddressInterface
     */
    public function setSuffix(?string $suffix = null): CustomerAddressInterface;

    /**
     * GetCompany
     *
     * @return string|null
     */
    public function getCompany(): ?string;

    /**
     * SetCompany
     *
     * @param string|null $company
     *
     * @return \Emartech\Emarsys\Api\Data\CustomerAddressInterface
     */
    public function setCompany(?string $company = null): CustomerAddressInterface;

    /**
     * GetStreet
     *
     * @return string|null
     */
    public function getStreet(): ?string;

    /**
     * SetStreet
     *
     * @param string|null $street
     *
     * @return \Emartech\Emarsys\Api\Data\CustomerAddressInterface
     */
    public function setStreet(?string $street = null): CustomerAddressInterface;

    /**
     * GetCity
     *
     * @return string|null
     */
    public function getCity(): ?string;

    /**
     * SetCity
     *
     * @param string|null $city
     *
     * @return \Emartech\Emarsys\Api\Data\CustomerAddressInterface
     */
    public function setCity(?string $city = null): CustomerAddressInterface;

    /**
     * GetCountryId
     *
     * @return string|null
     */
    public function getCountryId(): ?string;

    /**
     * SetCountryId
     *
     * @param string|null $countryId
     *
     * @return \Emartech\Emarsys\Api\Data\CustomerAddressInterface
     */
    public function setCountryId(?string $countryId = null): CustomerAddressInterface;

    /**
     * GetRegion
     *
     * @return string|null
     */
    public function getRegion(): ?string;

    /**
     * SetRegion
     *
     * @param string|null $region
     *
     * @return \Emartech\Emarsys\Api\Data\CustomerAddressInterface
     */
    public function setRegion(?string $region = null): CustomerAddressInterface;

    /**
     * GetPostcode
     *
     * @return string|null
     */
    public function getPostcode(): ?string;

    /**
     * SetPostcode
     *
     * @param string|null $postCode
     *
     * @return \Emartech\Emarsys\Api\Data\CustomerAddressInterface
     */
    public function setPostcode(?string $postCode = null): CustomerAddressInterface;

    /**
     * GetTelephone
     *
     * @return string|null
     */
    public function getTelephone(): ?string;

    /**
     * SetTelephone
     *
     * @param string|null $telephone
     *
     * @return \Emartech\Emarsys\Api\Data\CustomerAddressInterface
     */
    public function setTelephone(?string $telephone = null): CustomerAddressInterface;

    /**
     * GetFax
     *
     * @return string|null
     */
    public function getFax(): ?string;

    /**
     * SetFax
     *
     * @param string|null $fax
     *
     * @return \Emartech\Emarsys\Api\Data\CustomerAddressInterface
     */
    public function setFax(?string $fax = null): CustomerAddressInterface;

    /**
     * SetExtraFields
     *
     * @param \Emartech\Emarsys\Api\Data\ExtraFieldsInterface[]|null $extraFields
     *
     * @return \Emartech\Emarsys\Api\Data\CustomerAddressInterface
     */
    public function setExtraFields(?array $extraFields = null): CustomerAddressInterface;
}

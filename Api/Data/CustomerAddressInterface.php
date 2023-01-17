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
    public function setPrefix(string $prefix = null): CustomerAddressInterface;

    /**
     * GetFirstname
     *
     * @return string
     */
    public function getFirstname(): string;

    /**
     * SetFirstname
     *
     * @param string $firstName
     *
     * @return \Emartech\Emarsys\Api\Data\CustomerAddressInterface
     */
    public function setFirstname(string $firstName): CustomerAddressInterface;

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
    public function setMiddlename(string $middleName = null): CustomerAddressInterface;

    /**
     * GetLastname
     *
     * @return string
     */
    public function getLastname(): string;

    /**
     * SetLastname
     *
     * @param string $lastName
     *
     * @return \Emartech\Emarsys\Api\Data\CustomerAddressInterface
     */
    public function setLastname(string $lastName): CustomerAddressInterface;

    /**
     * GetSuffix
     *
     * @return string|null
     */
    public function getSuffix(): ?string;

    /**
     * GetExtraFields
     *
     * @return \Emartech\Emarsys\Api\Data\ExtraFieldsInterface[]
     */
    public function getExtraFields(): array;

    /**
     * SetSuffix
     *
     * @param string|null $suffix
     *
     * @return \Emartech\Emarsys\Api\Data\CustomerAddressInterface
     */
    public function setSuffix(string $suffix = null): CustomerAddressInterface;

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
    public function setCompany(string $company = null): CustomerAddressInterface;

    /**
     * GetStreet
     *
     * @return string
     */
    public function getStreet(): string;

    /**
     * SetStreet
     *
     * @param string $street
     *
     * @return \Emartech\Emarsys\Api\Data\CustomerAddressInterface
     */
    public function setStreet(string $street): CustomerAddressInterface;

    /**
     * GetCity
     *
     * @return string
     */
    public function getCity(): string;

    /**
     * SetCity
     *
     * @param string $city
     *
     * @return \Emartech\Emarsys\Api\Data\CustomerAddressInterface
     */
    public function setCity(string $city): CustomerAddressInterface;

    /**
     * GetCountryId
     *
     * @return string
     */
    public function getCountryId(): string;

    /**
     * SetCountryId
     *
     * @param string $countryId
     *
     * @return \Emartech\Emarsys\Api\Data\CustomerAddressInterface
     */
    public function setCountryId(string $countryId): CustomerAddressInterface;

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
    public function setRegion(string $region = null): CustomerAddressInterface;

    /**
     * GetPostcode
     *
     * @return string
     */
    public function getPostcode(): string;

    /**
     * SetPostcode
     *
     * @param string $postCode
     *
     * @return \Emartech\Emarsys\Api\Data\CustomerAddressInterface
     */
    public function setPostcode(string $postCode = null): CustomerAddressInterface;

    /**
     * GetTelephone
     *
     * @return string
     */
    public function getTelephone(): string;

    /**
     * SetTelephone
     *
     * @param string $telephone
     *
     * @return \Emartech\Emarsys\Api\Data\CustomerAddressInterface
     */
    public function setTelephone(string $telephone): CustomerAddressInterface;

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
    public function setFax(string $fax = null): CustomerAddressInterface;

    /**
     * SetExtraFields
     *
     * @param \Emartech\Emarsys\Api\Data\ExtraFieldsInterface[] $extraFields
     *
     * @return \Emartech\Emarsys\Api\Data\CustomerAddressInterface
     */
    public function setExtraFields(array $extraFields): CustomerAddressInterface;
}

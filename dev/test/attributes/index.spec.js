'use strict';

const defaultCustomerAttributes = [
  { code: 'website_id', name: 'Associate to Website' },
  { code: 'store_id', name: 'Create In' },
  { code: 'created_in', name: 'Created From' },
  { code: 'prefix', name: 'Name Prefix' },
  { code: 'firstname', name: 'First Name' },
  { code: 'middlename', name: 'Middle Name/Initial' },
  { code: 'lastname', name: 'Last Name' },
  { code: 'suffix', name: 'Name Suffix' },
  { code: 'email', name: 'Email' },
  { code: 'group_id', name: 'Group' },
  { code: 'dob', name: 'Date of Birth' },
  { code: 'default_billing', name: 'Default Billing Address' },
  { code: 'default_shipping', name: 'Default Shipping Address' },
  { code: 'taxvat', name: 'Tax/VAT Number' },
  { code: 'confirmation', name: 'Is Confirmed' },
  { code: 'created_at', name: 'Created At' },
  { code: 'gender', name: 'Gender' },
  { code: 'disable_auto_group_change', name: 'Disable Automatic Group Change Based on VAT ID' },
  { code: 'updated_at', name: 'Updated At' },
  { code: 'failures_num', name: 'Failures Number' },
  { code: 'first_failure', name: 'First Failure Date' },
  { code: 'lock_expires', name: 'Failures Number' }
];

const defaultCustomerAddressAttributes = [
  { code: 'city', name: 'City' },
  { code: 'company', name: 'Company' },
  { code: 'country_id', name: 'Country' },
  { code: 'fax', name: 'Fax' },
  { code: 'firstname', name: 'First Name' },
  { code: 'lastname', name: 'Last Name' },
  { code: 'middlename', name: 'Middle Name/Initial' },
  { code: 'postcode', name: 'Zip/Postal Code' },
  { code: 'prefix', name: 'Name Prefix' },
  { code: 'region', name: 'State/Province' },
  { code: 'region_id', name: 'State/Province' },
  { code: 'street', name: 'Street Address' },
  { code: 'suffix', name: 'Name Suffix' },
  { code: 'telephone', name: 'Phone Number' },
  { code: 'vat_id', name: 'VAT Number' },
  { code: 'vat_is_valid', name: 'VAT number validity' },
  { code: 'vat_request_date', name: 'VAT number validation request date' },
  { code: 'vat_request_id', name: 'VAT number validation request ID' },
  { code: 'vat_request_success', name: 'VAT number validation request success' }
];

const websiteId = 1;

describe('Attributes endpoint', function() {
  afterEach(async function() {});

  after(async function() {});

  describe('get', function() {
    it('should fetch attributes for customer', async function() {
      const { attributes } = await this.magentoApi.execute('attributes', 'get', { type: 'customer' });
      const mappedAttributes = attributes.map(attribute => {
        return { code: attribute.code, name: attribute.name };
      });

      expect(mappedAttributes).to.eql(defaultCustomerAttributes);
    });

    it('should fetch attributes for customer_address', async function() {
      const { attributes } = await this.magentoApi.execute('attributes', 'get', { type: 'customer_address' });
      const mappedAttributes = attributes.map(attribute => {
        return { code: attribute.code, name: attribute.name };
      });

      expect(mappedAttributes).to.eql(defaultCustomerAddressAttributes);
    });
  });

  describe('set', function() {
    it('should modify customer attribute config for website', async function() {
      await this.magentoApi.execute('attributes', 'set', {
        websiteId,
        type: 'customer',
        attributeCodes: ['hello_attribute']
      });

      const config = await this.db
        .select()
        .from(this.getTableName('core_config_data'))
        .where('scope_id', websiteId)
        .andWhere('path', 'emartech/emarsys/config/customer_attributes')
        .first();

      expect(config.value).to.equal(JSON.stringify(['hello_attribute']));
    });

    it('should modify customer_address attribute config for website', async function() {
      await this.magentoApi.execute('attributes', 'set', {
        websiteId,
        type: 'customer_address',
        attributeCodes: ['hello_attribute']
      });

      const config = await this.db
        .select()
        .from(this.getTableName('core_config_data'))
        .where('scope_id', websiteId)
        .andWhere('path', 'emartech/emarsys/config/customer_address_attributes')
        .first();

      expect(config.value).to.equal(JSON.stringify(['hello_attribute']));
    });
  });
});

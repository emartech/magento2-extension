'use strict';

const { defaultCustomerAttributes, defaultCustomerAddressAttributes } = require('./attributes');

const websiteId = 1;

describe.only('Attributes endpoint', function() {
  afterEach(async function() {});

  after(async function() {});

  describe('get', function() {
    it('should fetch attributes including extra fields for customer', async function() {
      const { attributes } = await this.magentoApi.execute('attributes', 'get', { type: 'customer' });
      const mappedAttributes = attributes.map(attribute => {
        return { code: attribute.code, name: attribute.name };
      });

      if (this.magentoVersion.startsWith('2.1')) {
        expect(mappedAttributes).to.eql(defaultCustomerAttributes.old);
      } else {
        expect(mappedAttributes).to.eql(defaultCustomerAttributes.new);
      }
    });

    it('should fetch attributes including extra fields for customer_address', async function() {
      const { attributes } = await this.magentoApi.execute('attributes', 'get', { type: 'customer_address' });
      const mappedAttributes = attributes.map(attribute => {
        return { code: attribute.code, name: attribute.name };
      });

      if (this.magentoVersion.startsWith('2.1')) {
        expect(mappedAttributes).to.eql(defaultCustomerAddressAttributes.old);
      } else {
        expect(mappedAttributes).to.eql(defaultCustomerAddressAttributes.new);
      }
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

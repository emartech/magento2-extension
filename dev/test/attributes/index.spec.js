'use strict';

const { customerAttributes, customerAddressAttributes } = require('./attributes');

const websiteId = 1;

const mapAttributes = attributes => attributes.map(attribute => ({ code: attribute.code, name: attribute.name }));

describe('Attributes endpoint', function() {
  afterEach(async function() {});

  after(async function() {});

  describe('get', function() {
    it('should fetch attributes including extra fields for customer', async function() {
      const { attributes } = await this.magentoApi.execute('attributes', 'get', { type: 'customer' });
      const mappedAttributes = mapAttributes(attributes);

      if (this.magentoVersion.startsWith('2.1')) {
        expect(mappedAttributes).to.have.deep.members(customerAttributes.old);
      } else if (this.magentoEdition === 'Enterprise') {
        expect(mappedAttributes).to.have.deep.members(customerAttributes.enterprise);
      } else {
        expect(mappedAttributes).to.have.deep.members(customerAttributes.new);
      }
    });

    it('should fetch attributes including extra fields for customer_address', async function() {
      const { attributes } = await this.magentoApi.execute('attributes', 'get', { type: 'customer_address' });
      const mappedAttributes = mapAttributes(attributes);

      if (this.magentoVersion.startsWith('2.1')) {
        expect(mappedAttributes).to.have.deep.members(customerAddressAttributes.old);
      } else {
        expect(mappedAttributes).to.have.deep.members(customerAddressAttributes.new);
      }
    });

    it('should fetch attributes including extra fields for products', async function() {
      const { attributes } = await this.magentoApi.execute('attributes', 'get', { type: 'product' });
      const mappedAttributes = attributes.map(attribute => {
        return { code: attribute.code, name: attribute.name };
      });

      const productExtraAttributes = [
        { code: 'emarsys_test_fuel_type', name: 'Fuel Type' },
        { code: 'emarsys_test_gearbox', name: 'Gearbox' },
        { code: 'emarsys_test_number_of_doors', name: 'Number Of Doors' },
        { code: 'emarsys_test_number_of_seats', name: 'Number Of Seats' },
        { code: 'emarsys_test_vehicle_type', name: 'Vehicle Type' }
      ];

      expect(mappedAttributes).to.containSubset(productExtraAttributes);
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

    it('should modify product attribute config for website', async function() {
      await this.magentoApi.execute('attributes', 'set', {
        websiteId: 0,
        type: 'product',
        attributeCodes: ['hello_attribute']
      });

      const config = await this.db
        .select()
        .from(this.getTableName('core_config_data'))
        .where('scope_id', 0)
        .andWhere('path', 'emartech/emarsys/config/product_attributes')
        .first();

      expect(config.value).to.equal(JSON.stringify(['hello_attribute']));
    });
  });
});

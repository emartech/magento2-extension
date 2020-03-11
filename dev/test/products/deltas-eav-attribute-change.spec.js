'use strict';

describe('Deltas EAV Attribute Change', function() {
  const sku = '24-MB03';
  const eavCode = 'emarsys_test_fuel_type';

  before(async function() {
    await this.magentoApi.execute('attributes', 'set', {
      websiteId: 0,
      type: 'product',
      attributeCodes: [eavCode]
    });

    await this.magentoApi.execute('config', 'set', {
      websiteId: 1,
      config: {
        productDeltaSync: 'enabled'
      }
    });
  });

  after(async function() {
    await this.magentoApi.execute('attributes', 'set', {
      websiteId: 0,
      type: 'product',
      attributeCodes: []
    });

    await this.magentoApi.execute('config', 'set', {
      websiteId: 1,
      config: {
        productDeltaSync: 'disabled'
      }
    });
  });

  it('should return eav attribute changed product', async function() {
    const eavValue = 'deltas_test';

    await this.magentoApi.put({
      path: `/rest/V1/products/${sku}`,
      payload: {
        product: {
          custom_attributes: [
            {
              attribute_code: eavCode,
              value: eavValue
            }
          ]
        }
      }
    });

    await this.reindex();

    const { products: productDeltas } = await this.magentoApi.execute('products', 'getDeltas', {
      page: 1,
      limit: 3,
      storeIds: [1, 2],
      sinceId: 0
    });

    const eavValueInStore = productDeltas.find(product => product.sku === sku)
      .store_data.find(data => data.store_id === 1)
      .extra_fields.find(field => field.key === eavCode)
      .value;

    expect(productDeltas.length).to.be.equal(1);
    expect(eavValueInStore).to.be.equal(eavValue);
  });
});

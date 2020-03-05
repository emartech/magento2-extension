'use strict';

describe('Deltas Catalog Change', function() {
  const sku = '24-MB03';

  before(async function() {
    await this.magentoApi.execute('config', 'set', {
      websiteId: 1,
      config: {
        productDeltaSync: 'enabled'
      }
    });
  });

  after(async function() {
    await this.magentoApi.execute('config', 'set', {
      websiteId: 1,
      config: {
        productDeltaSync: 'disabled'
      }
    });
  });

  it('should return catalog changed product', async function() {
    const name = 'Product Delta test';
    await this.magentoApi.put({
      path: `/rest/V1/products/${sku}`,
      payload: {
        product: {
          name
        }
      }
    });

    const { products: productDeltas } = await this.magentoApi.execute('products', 'getDeltas', {
      page: 1,
      limit: 3,
      storeIds: [1, 2],
      sinceId: 0
    });

    const nameInStore = productDeltas.find(product => product.sku === sku)
      .store_data.find(data => data.store_id === 1)
      .name;

    expect(productDeltas.length).to.be.equal(1);
    expect(nameInStore).to.be.equal(name);
  });
});

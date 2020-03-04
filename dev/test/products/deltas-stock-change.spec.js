'use strict';

describe('Deltas Stock Change', function() {
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

  afterEach(async function() {
    await this.magentoApi.put({
      path: `/rest/V1/products/${sku}/stockItems/1`,
      payload: {
        stockItem: {
          qty: 100,
          is_in_stock: true
        }
      }
    });
  });

  it('should return stock changed product through product API', async function() {
    await this.magentoApi.put({
      path: `/rest/V1/products/${sku}`,
      payload: {
        product: {
          extension_attributes: {
            stockItem: {
              qty: 998
            }
          }
        }
      }
    });

    const { products: productDeltas } = await this.magentoApi.execute('products', 'getDeltas', {
      page: 1,
      limit: 3,
      storeIds: [1, 2],
      sinceId: 0
    });

    const product = productDeltas.find(product => product.sku === sku);

    expect(productDeltas.length).to.be.equal(1);
    expect(product.qty).to.be.equal(998);
  });

  it('should return stock changed product through stock API', async function() {
    await this.magentoApi.put({
      path: `/rest/V1/products/${sku}/stockItems/1`,
      payload: {
        stockItem: {
          qty: 999,
          is_in_stock: true
        }
      }
    });

    const { products: productDeltas } = await this.magentoApi.execute('products', 'getDeltas', {
      page: 1,
      limit: 3,
      storeIds: [1, 2],
      sinceId: 0
    });

    const product = productDeltas.find(product => product.sku === sku);

    expect(productDeltas.length).to.be.equal(1);
    expect(product.qty).to.be.equal(999);
  });

  it('should return out of stock products', async function() {
    await this.magentoApi.put({
      path: `/rest/V1/products/${sku}/stockItems/1`,
      payload: {
        stockItem: {
          qty: 0
        }
      }
    });

    const { products: productDeltas } = await this.magentoApi.execute('products', 'getDeltas', {
      page: 1,
      limit: 3,
      storeIds: [1, 2],
      sinceId: 0
    });
    const product = productDeltas.find(product => product.sku === sku);

    expect(productDeltas.length).to.be.equal(1);
    expect(product).not.to.be.undefined;
  });
});

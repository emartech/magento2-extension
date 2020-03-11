'use strict';

describe('Deltas Price Change', function() {
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

  it('should return price changed product through base-price endpoint', async function() {
    const { price: currentPrice } = await this.magentoApi.get({ path: `/rest/V1/products/${sku}` });
    const newPrice = currentPrice - 1;

    await this.magentoApi.post({
      path: '/rest/V1/products/base-prices',
      payload: {
        prices: [
          {
            price: newPrice,
            store_id: 1,
            sku
          }
        ]
      }
    });

    await this.reindex();

    const { products: productDeltas } = await this.magentoApi.execute('products', 'getDeltas', {
      page: 1,
      limit: 3,
      storeIds: [1, 2],
      sinceId: 0
    });

    expect(productDeltas.length).to.be.equal(1);

    const priceInStore = productDeltas.find(product => product.sku === sku)
      .store_data.find(data => data.store_id === 1)
      .webshop_price;
    expect(priceInStore).to.be.equal(newPrice);
  });

  it('should return price changed product through special-price endpoint', async function() {
    const { price: currentPrice } = await this.magentoApi.get({ path: `/rest/V1/products/${sku}` });
    const newPrice = currentPrice - 1;

    await this.magentoApi.post({
      path: '/rest/V1/products/special-price',
      payload: {
        prices: [
          {
            price: newPrice,
            store_id: 1,
            sku
          }
        ]
      }
    });

    await this.reindex();

    const { products: productDeltas } = await this.magentoApi.execute('products', 'getDeltas', {
      page: 1,
      limit: 3,
      storeIds: [1, 2],
      sinceId: 0
    });
    expect(productDeltas.length).to.be.equal(1);

    const productInStore = productDeltas.find(product => product.sku === sku)
      .store_data.find(data => data.store_id === 1);
    expect(productInStore.webshop_price).to.be.equal(newPrice);
    expect(productInStore.original_webshop_price).to.be.equal(currentPrice);
  });

  it('should return price changed product through products endpoint', async function() {
    const { price: currentPrice } = await this.magentoApi.get({ path: `/rest/V1/products/${sku}` });
    const newPrice = currentPrice - 1;

    await this.magentoApi.put({
      path: `/rest/V1/products/${sku}`,
      payload: {
        product: { price: newPrice }
      }
    });

    await this.reindex();

    const { products: productDeltas } = await this.magentoApi.execute('products', 'getDeltas', {
      page: 1,
      limit: 3,
      storeIds: [1, 2],
      sinceId: 0
    });
    expect(productDeltas.length).to.be.equal(1);

    const priceInStore = productDeltas.find(product => product.sku === sku)
      .store_data.find(data => data.store_id === 1)
      .webshop_price;
    expect(priceInStore).to.be.equal(newPrice);
  });
});

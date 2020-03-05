'use strict';

const { insertProductDeltas } = require('../helpers/product-deltas');

describe('Product Deltas endpoint', function() {
  it('should return the same response as the product api', async function() {
    const sku = '24-MB01';
    await insertProductDeltas(this.db, [sku]);

    const { products: productsDeltaReponse } = await this.magentoApi.execute('products', 'getDeltas', {
      page: 1,
      storeIds: [1],
      sinceId: 0
    });

    const { products: productsApiResponse } = await this.magentoApi.execute('products', 'get', {
      page: 1,
      limit: 100,
      storeIds: [1]
    });
    const product = productsApiResponse.find(product => product.sku === sku);

    expect(product).to.eql(productsDeltaReponse[0]);
  });
});

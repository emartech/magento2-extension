'use strict';

const { getTableName } = require('../helpers/get-table-name');

describe('Deltas Catalog Change', function() {
  const sku = '24-MB03';

  before(async function() {
    await this.db(getTableName('emarsys_product_delta')).truncate();
    await this.db(getTableName('emarsys_delta_check_cl')).truncate();
  });

  it('should not add entries to the deltas table', async function() {
    await this.magentoApi.execute('config', 'set', {
      websiteId: 1,
      config: { productDeltaSync: 'disabled' }
    });

    await this.magentoApi.put({
      path: `/rest/V1/products/${sku}`,
      payload: { product: { name: 'some cool product name...' } }
    });

    await this.reindex();

    await this.magentoApi.execute('products', 'getDeltas', {
      page: 1,
      limit: 10,
      storeIds: [1],
      sinceId: 0
    });

    const storedDeltas = await this.db(getTableName('emarsys_product_delta')).select();

    expect(storedDeltas.length).to.be.equal(0);
  });

  it('should cleanup changes log table after indexing', async function() {
    await this.magentoApi.execute('config', 'set', {
      websiteId: 1,
      config: { productDeltaSync: 'disabled' }
    });

    await this.magentoApi.put({
      path: `/rest/V1/products/${sku}`,
      payload: { product: { name: 'another cool product name...' } }
    });

    await this.reindex();

    await this.magentoApi.execute('products', 'getDeltas', {
      page: 1,
      limit: 10,
      storeIds: [1],
      sinceId: 0
    });

    const changesToBeIndexed = await this.db(getTableName('emarsys_delta_check_cl')).select();

    expect(changesToBeIndexed.length).to.be.equal(0);
  });
});

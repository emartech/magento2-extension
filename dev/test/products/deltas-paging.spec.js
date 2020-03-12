'use strict';

const { insertProductDeltas } = require('../helpers/product-deltas');
const { getTableName } = require('../helpers/get-table-name');

describe('Product Deltas endpoint', function() {
  it('should page through all deltas', async function() {
    const skus = ['24-MB01', '24-MB04', '24-MB03'];
    await insertProductDeltas(this.db, skus);

    const { products: productsOnFirstPage, maxId } = await this.magentoApi.execute('products', 'getDeltas', {
      limit: 2,
      page: 1,
      storeIds: [1],
      sinceId: 0
    });

    expect(productsOnFirstPage.map(product => product.sku)).to.have.members(['24-MB01', '24-MB04']);

    const { products: productsOnSecondPage } = await this.magentoApi.execute('products', 'getDeltas', {
      limit: 2,
      page: 2,
      storeIds: [1],
      sinceId: 0,
      maxId
    });

    expect(productsOnSecondPage.map(product => product.sku)).to.have.members(['24-MB03']);
  });

  it('should delete rows below sinceId', async function() {
    const skus = ['24-MB01', '24-MB04', '24-MB03'];
    await insertProductDeltas(this.db, skus);

    const { products } = await this.magentoApi.execute('products', 'getDeltas', {
      limit: 2,
      page: 1,
      storeIds: [1],
      sinceId: 1
    });

    const rows = await this.db(getTableName('emarsys_product_delta')).select('sku');

    expect(rows.map(row => row.sku)).not.to.include('24-MB01');
    expect(products.map(product => product.sku)).to.have.members(['24-MB04', '24-MB03']);
  });

  it('should squash changes of the same products', async function() {
    const skus = ['24-MB01', '24-MB04', '24-MB01', '24-MB03'];
    await insertProductDeltas(this.db, skus);

    const { products, lastPage } = await this.magentoApi.execute('products', 'getDeltas', {
      limit: 3,
      page: 1,
      storeIds: [1],
      sinceId: 0
    });

    expect(products.map(product => product.sku)).to.have.members(['24-MB04', '24-MB01', '24-MB03']);
    expect(lastPage).to.eql(1);
  });

  it('should squash items only between sinceId and maxId', async function() {
    const skus = ['24-MB01', '24-MB01', '24-MB03', '24-MB01'];
    await insertProductDeltas(this.db, skus);

    const { products } = await this.magentoApi.execute('products', 'getDeltas', {
      limit: 2,
      page: 1,
      storeIds: [1],
      sinceId: 0,
      maxId: 3
    });

    const rows = await this.db(getTableName('emarsys_product_delta'))
      .select('sku')
      .where('product_delta_id', '>', '3');
    expect(rows.map(row => row.sku)).to.include('24-MB01');

    expect(products.map(product => product.sku)).to.have.members(['24-MB01', '24-MB03']);
  });

  it('should not page beyond the maxId', async function() {
    const skus = ['24-MB01', '24-MB03', '24-MB04'];
    await insertProductDeltas(this.db, skus);

    const { products } = await this.magentoApi.execute('products', 'getDeltas', {
      limit: 3,
      page: 1,
      storeIds: [1],
      sinceId: 0,
      maxId: 2
    });

    expect(products.map(product => product.sku)).to.have.members(['24-MB01', '24-MB03']);
  });

  it('should respond with the given maxId', async function() {
    const skus = ['24-MB01', '24-MB03', '24-MB04'];
    await insertProductDeltas(this.db, skus);

    const { maxId } = await this.magentoApi.execute('products', 'getDeltas', {
      limit: 2,
      page: 1,
      storeIds: [1],
      sinceId: 0,
      maxId: 2
    });

    expect(maxId).to.eql(2);
  });

  it('should not squash items before the current page', async function() {
    const skus = ['24-MB01', '24-MB03', '24-MB04'];
    await insertProductDeltas(this.db, skus);

    const { products: productsOnFirstPage, lastPage: lastPageOnFirstTry } = await this.magentoApi.execute(
      'products',
      'getDeltas',
      {
        limit: 1,
        page: 1,
        storeIds: [1],
        sinceId: 0,
        maxId: 3
      }
    );

    expect(productsOnFirstPage.map(product => product.sku)).to.eql(['24-MB01']);
    expect(lastPageOnFirstTry).to.eql(3);

    await insertProductDeltas(this.db, ['24-MB01']);

    const { products: productsOnSecondPage, lastPage: lastPageOnSecondTry } = await this.magentoApi.execute(
      'products',
      'getDeltas',
      {
        limit: 1,
        page: 2,
        storeIds: [1],
        sinceId: 0,
        maxId: 3
      }
    );

    expect(productsOnSecondPage.map(product => product.sku)).to.eql(['24-MB03']);
    expect(lastPageOnSecondTry).to.eql(3);
  });

  it('should return pageCount', async function() {
    const skus = ['24-MB01', '24-MB03', '24-MB04', '24-MB01', '24-MB04'];
    await insertProductDeltas(this.db, skus);

    const { lastPage } = await this.magentoApi.execute('products', 'getDeltas', {
      limit: 2,
      page: 1,
      storeIds: [1],
      sinceId: 0
    });

    expect(lastPage).to.eql(2);
  });

  it('should respond with 406 if autoincrement is reset', async function() {
    let errorThrown;

    try {
      await this.magentoApi.execute('products', 'getDeltas', {
        limit: 2,
        page: 1,
        storeIds: [1],
        sinceId: 98642343
      });
    } catch (error) {
      errorThrown = error;
    }

    expect(errorThrown.response.status).to.equal(406);
  });
});

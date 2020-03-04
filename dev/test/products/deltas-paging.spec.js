'use strict';

const insertProductDeltas = async (db, skus) => {
  const deltas = [];

  for (const sku of skus) {
    const productEntityRow = await db('catalog_product_entity')
      .select('entity_id')
      .where({ sku })
      .first();
    deltas.push({ sku, entity_id: productEntityRow.entity_id, row_id: productEntityRow.entity_id });
  }

  return db('emarsys_product_delta').insert(deltas);
};

describe('Product Deltas endpoint', function() {
  before(async function() {});

  after(async function() {});

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
});

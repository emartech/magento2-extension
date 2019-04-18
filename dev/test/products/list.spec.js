'use strict';

const { getProducts } = require('../fixtures/products');

describe('Products endpoint', function() {
  it('returns product count and products according to page and page_size', async function() {
    const page = 3;
    const limit = 10;

    const { products, productCount } = await this.magentoApi.execute('products', 'get', { page, limit });
    const { products: expectedProducts, productCount: expectedProductCount } = getProducts(this.hostname, page, limit);

    const product = products[0];
    const expectedProduct = expectedProducts[0];

    expect(products.length).to.equal(expectedProducts.length);
    expect(productCount).to.equal(expectedProductCount);

    ['entity_id', 'type', 'sku', 'qty', 'is_in_stock', 'images'].forEach(key => {
      expect(product[key]).to.eql(expectedProduct[key]);
    });

    expect(product.children_entity_ids).to.be.an('array');
    expect(product.categories[0]).to.equal(expectedProduct.categories[0]);
    expect(product.categories[1]).to.equal(expectedProduct.categories[1]);

    const storeLevelProduct = product.store_data[0];
    ['name', 'price', 'link', 'status', 'description'].forEach(key => {
      expect(storeLevelProduct[key]).to.equal(expectedProduct.store_data[0][key]);
    });
  });

  it('returns child entities for configurable products', async function() {
    const page = this.magentoVersion === '2.3.0' ? 68 : 67;
    const limit = 1;

    const { products } = await this.magentoApi.execute('products', 'get', { page, limit });
    const { products: expectedProducts } = getProducts(this.hostname, 67, limit, this.magentoVersion);
    const product = products[0];
    const expectedProduct = expectedProducts[0];

    ['type', 'children_entity_ids'].forEach(key => {
      expect(product[key]).to.eql(expectedProduct[key]);
    });
  });
});

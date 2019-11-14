'use strict';

const axios = require('axios');

const { getProducts } = require('../fixtures/products');

const getAxiosInstance = (hostname, token) =>
  axios.create({
    baseURL: `http://${hostname}`,
    headers: { Authorization: `Bearer ${token}` },
    timeout: 30000
  });

const getFirstProduct = async (hostname, token) => {
  const response = await getAxiosInstance(hostname, token).get(
    'index.php/rest/V1/emarsys/products?page=1&page_size=1&store_id=0,1'
  );
  return response.data.products[0];
};

describe('Products endpoint', function() {

  before(async function() {
    await this.magentoApi.execute('attributes', 'set', {
      websiteId: 0,
      type: 'product',
      attributeCodes: ['emarsys_test_fuel_type']
    });
  });

  after(async function() {
    await this.magentoApi.execute('attributes', 'set', {
      websiteId: 0,
      type: 'product',
      attributeCodes: []
    });
  });

  it('returns product count and products according to page and page_size', async function() {
    const page = 3;
    const limit = 10;

    const { products: expectedProducts, productCount: expectedProductCount } = getProducts(
      this.hostname,
      page,
      limit,
      this.magentoVersion,
      this.magentoEdition
    );

    const expectedProduct = expectedProducts[0];

    await this.createProduct({
      sku: expectedProduct.sku,
      custom_attributes: {
        special_price: 2
      }
    });

    const { products, productCount } = await this.magentoApi.execute('products', 'get', { page, limit });

    const product = products[0];

    expect(products.length).to.equal(expectedProducts.length);
    expect(productCount).to.equal(expectedProductCount);

    ['entity_id', 'type', 'sku', 'qty', 'is_in_stock', 'images'].forEach(key => {
      expect(product[key]).to.eql(expectedProduct[key]);
    });

    expect(product.children_entity_ids).to.be.an('array');
    expect(product.categories[0]).to.equal(expectedProduct.categories[0]);
    expect(product.categories[1]).to.equal(expectedProduct.categories[1]);

    const storeLevelProduct = product.store_data[0];
    ['name', 'display_price', 'original_display_price', 'link', 'status', 'description'].forEach(key => {
      expect(storeLevelProduct[key]).to.equal(expectedProduct.store_data[0][key]);
    });
  });

  it('returns child entities for configurable products', async function() {
    let page;
    switch (this.magentoVersion) {
      case '2.3.0':
        page = 68;
        break;
      case '2.3.1':
        page = this.magentoEdition === 'Enterprise' ? 70 : 68;
        break;
      case '2.3.2':
        page = 68; //this.magentoEdition === 'Enterprise' ? 70 : 68;
        break;
      case '2.3.3':
        page = 68; //this.magentoEdition === 'Enterprise' ? 70 : 68;
        break;
      default:
        page = 67;
    }

    const limit = 1;

    const { products } = await this.magentoApi.execute('products', 'get', { page, limit });
    const { products: expectedProducts } = getProducts(
      this.hostname,
      67,
      limit,
      this.magentoVersion,
      this.magentoEdition
    );
    const product = products[0];
    const expectedProduct = expectedProducts[0];

    ['type', 'children_entity_ids'].forEach(key => {
      expect(product[key]).to.eql(expectedProduct[key]);
    });
  });

  it('returns extra_fields for products', async function() {
    const product = await getFirstProduct(this.hostname, this.token);

    const path = `index.php/rest/V1/products/${product.sku}`;
    const payload = {
      product: {
        custom_attributes: [
          {
            attribute_code: 'emarsys_test_fuel_type',
            value: 'gasoline'
          },
          {
            attribute_code: 'emarsys_test_number_of_seats',
            value: 6
          }
        ]
      }
    };
    await getAxiosInstance(this.hostname, this.token).put(path, payload);
    const updatedProduct = await getFirstProduct(this.hostname, this.token);

    expect(updatedProduct.store_data[0].extra_fields[0].key).to.be.equal('emarsys_test_fuel_type');
    expect(updatedProduct.store_data[0].extra_fields[0].value).to.be.equal('gasoline');
    expect(updatedProduct.store_data[0].extra_fields.length).to.be.equal(1);
  });
});

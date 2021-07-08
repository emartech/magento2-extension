'use strict';

const { getProducts } = require('../fixtures/products');

describe('Products endpoint', function () {
  before(async function () {
    await this.magentoApi.execute('attributes', 'set', {
      websiteId: 0,
      type: 'product',
      attributeCodes: ['emarsys_test_fuel_type', 'country_of_manufacture']
    });
  });

  after(async function () {
    await this.magentoApi.execute('attributes', 'set', {
      websiteId: 0,
      type: 'product',
      attributeCodes: []
    });
  });

  it('returns product count and products according to page and page_size', async function () {
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
      custom_attributes: [
        {
          attribute_code: 'special_price',
          value: 2
        }
      ]
    });

    const { products, productCount } = await this.magentoApi.execute('products', 'get', { page, limit, storeIds: [1] });

    const product = products[0];

    expect(products.length).to.equal(expectedProducts.length);
    expect(productCount).to.equal(expectedProductCount);

    ['entity_id', 'type', 'sku', 'qty', 'is_in_stock', 'images'].forEach((key) => {
      expect({ key, value: product[key] }).to.eql({ key, value: expectedProduct[key] });
    });

    expect(product.children_entity_ids).to.be.an('array');
    expect(product.categories[0]).to.equal(expectedProduct.categories[0]);
    expect(product.categories[1]).to.equal(expectedProduct.categories[1]);

    const storeLevelProduct = product.store_data.find((store) => store.store_id === 1);
    [
      'name',
      'price',
      'webshop_price',
      'original_webshop_price',
      'original_display_price',
      'display_webshop_price',
      'link',
      'status',
      'description'
    ].forEach((key) => {
      expect({ key, value: storeLevelProduct[key] }).to.eql({ key, value: expectedProduct.store_data[0][key] });
    });
  });

  it('returns child entities for configurable products', async function () {
    let page;
    switch (this.magentoVersion) {
      case '2.3.0':
        page = 68;
        break;
      case '2.3.1':
        page = this.magentoEdition === 'Enterprise' ? 70 : 68;
        break;
      case '2.1.9':
        page = this.magentoEdition === 'Enterprise' ? 69 : 68;
        break;
      case '2.3.2':
        page = 68; //this.magentoEdition === 'Enterprise' ? 70 : 68;
        break;
      case '2.3.3':
        page = 68;
        break;
      case '2.3.4':
        page = 68;
        break;
      case '2.3.5':
        page = 68;
        break;
      case '2.4.0':
        page = 68;
        break;
      default:
        page = 67;
    }

    const limit = 1;

    const { products } = await this.magentoApi.execute('products', 'get', { page, limit, storeIds: [1] });
    const { products: expectedProducts } = getProducts(
      this.hostname,
      67,
      limit,
      this.magentoVersion,
      this.magentoEdition
    );
    const product = products[0];
    const expectedProduct = expectedProducts[0];

    ['type', 'children_entity_ids'].forEach((key) => {
      expect(product[key]).to.eql(expectedProduct[key]);
    });
  });

  it('returns extra_fields for products', async function () {
    const { products: originalProducts } = await this.magentoApi.execute('products', 'get', {
      page: 1,
      limit: 1,
      storeIds: [1]
    });

    await this.magentoApi.put({
      path: `/rest/V1/products/${originalProducts[0].sku}`,
      payload: {
        product: {
          custom_attributes: [
            {
              attribute_code: 'emarsys_test_fuel_type',
              value: 'gasoline'
            },
            {
              attribute_code: 'emarsys_test_number_of_seats',
              value: 6
            },
            {
              attribute_code: 'country_of_manufacture',
              value: 'AZ'
            }
          ]
        }
      }
    });

    const { products } = await this.magentoApi.execute('products', 'get', { page: 1, limit: 1, storeIds: [1] });
    const updatedProduct = products[0];

    expect(updatedProduct.store_data[0].extra_fields[0].key).to.be.equal('emarsys_test_fuel_type');
    expect(updatedProduct.store_data[0].extra_fields[0].value).to.be.equal('gasoline');

    expect(updatedProduct.store_data[0].extra_fields[1].key).to.be.equal('country_of_manufacture');
    expect(updatedProduct.store_data[0].extra_fields[1].value).to.be.equal('AZ');
    expect(updatedProduct.store_data[0].extra_fields[1].text_value).to.be.equal('Azerbaijan');
    expect(updatedProduct.store_data[0].extra_fields.length).to.be.equal(2);
  });

  it('returns different prices for the same product on multiple websites', async function () {
    const sku = '24-MB01';

    const extensionAttributes = this.magentoVersion.startsWith('2.1')
      ? {}
      : { extension_attributes: { website_ids: [1, 2] } };

    await this.magentoApi.post({
      path: '/rest/default/V1/products',
      payload: {
        product: {
          sku,
          price: 111,
          ...extensionAttributes
        }
      }
    });

    await this.magentoApi.post({
      path: '/rest/second_store/V1/products',
      payload: {
        product: {
          sku,
          price: 222,
          ...extensionAttributes
        }
      }
    });

    const { products } = await this.magentoApi.execute('products', 'get', { page: 1, limit: 10, storeIds: [1, 2] });

    const product = products.find((product) => product.sku === sku);
    const defaultStoreItem = product.store_data.find((storeData) => storeData.store_id === 1);
    const secondStoreItem = product.store_data.find((storeData) => storeData.store_id === 2);

    expect(defaultStoreItem.webshop_price).to.eql(111);
    expect(defaultStoreItem.display_webshop_price).to.eql(222);
    expect(defaultStoreItem.original_display_webshop_price).to.eql(222);

    expect(secondStoreItem.webshop_price).to.eql(222);
    expect(secondStoreItem.display_webshop_price).to.eql(444);
    expect(secondStoreItem.original_display_webshop_price).to.eql(444);
  });

  it('returns different original prices for the same product on multiple websites', async function () {
    const sku = '24-MB04';

    const extensionAttributes = this.magentoVersion.startsWith('2.1')
      ? {}
      : { extension_attributes: { website_ids: [1, 2] } };

    await this.magentoApi.post({
      path: '/rest/second_store/V1/products',
      payload: {
        product: {
          sku,
          price: 1000,
          ...extensionAttributes
        }
      }
    });

    const { products } = await this.magentoApi.execute('products', 'get', { page: 1, limit: 10, storeIds: [1, 2] });

    const product = products.find((product) => product.sku === sku);
    const defaultStoreItem = product.store_data.find((storeData) => storeData.store_id === 1);
    const secondStoreItem = product.store_data.find((storeData) => storeData.store_id === 2);

    expect(defaultStoreItem.display_webshop_price).to.eql(64);
    expect(defaultStoreItem.original_display_webshop_price).to.eql(64);
    expect(secondStoreItem.display_webshop_price).to.eql(64);
    expect(secondStoreItem.original_display_webshop_price).to.eql(2000);
  });

  it('returns product with status 0 if its not assigned to a website', async function () {
    const sku = '24-MB01';

    await this.magentoApi.delete({
      path: `/rest/default/V1/products/${sku}/websites/2`
    });

    const { products } = await this.magentoApi.execute('products', 'get', { page: 1, limit: 10, storeIds: [1, 2] });

    const product = products.find((product) => product.sku === sku);
    const defaultStoreItem = product.store_data.find((storeData) => storeData.store_id === 1);
    const secondStoreItem = product.store_data.find((storeData) => storeData.store_id === 2);

    expect(defaultStoreItem.status).to.eql(1);
    expect(secondStoreItem.status).to.eql(0);

    await this.magentoApi.post({
      path: `/rest/default/V1/products/${sku}/websites/`,
      payload: {
        productWebsiteLink: {
          sku,
          website_id: 2
        }
      }
    });
  });

  it('returns product images for stores', async function () {
    const sku = '24-MB04';

    const timestamp = new Date() * 1;
    const fileName = `my_custom_pic_${timestamp}.gif`;

    await this.magentoApi.post({
      path: `/rest/second_store/V1/products/${sku}/media`,
      payload: {
        entry: {
          media_type: 'image',
          label: 'Image',
          position: 100,
          disabled: false,
          types: ['image', 'small_image', 'thumbnail'],
          content: {
            base64_encoded_data: 'R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7',
            type: 'image/gif',
            name: fileName
          }
        }
      }
    });

    const { products } = await this.magentoApi.execute('products', 'get', { page: 1, limit: 10, storeIds: [1, 2] });

    const product = products.find((product) => product.sku === sku);

    const defaultStore = product.store_data.find((store) => store.store_id === 1);
    const secondStore = product.store_data.find((store) => store.store_id === 2);

    const expectedDefaultImages = {
      image: 'http://magento-test.local/pub/media/catalog/product/m/b/mb04-black-0.jpg',
      small_image: 'http://magento-test.local/pub/media/catalog/product/m/b/mb04-black-0.jpg',
      thumbnail: 'http://magento-test.local/pub/media/catalog/product/m/b/mb04-black-0.jpg'
    };

    expect(product.images).to.eql(expectedDefaultImages);
    expect(defaultStore.images).to.eql(expectedDefaultImages);
    expect(secondStore.images).to.eql({
      image: `http://magento-test.local/pub/media/catalog/product/m/y/${fileName}`,
      small_image: `http://magento-test.local/pub/media/catalog/product/m/y/${fileName}`,
      thumbnail: `http://magento-test.local/pub/media/catalog/product/m/y/${fileName}`
    });
  });

  context('out of stock', function () {
    const sku = '24-MB03';
    before(async function () {
      await this.magentoApi.put({
        path: `/rest/V1/products/${sku}/stockItems/1`,
        payload: {
          stockItem: {
            qty: 0
          }
        }
      });
    });

    after(async function () {
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

    it('should return out of stock products', async function () {
      const { products } = await this.magentoApi.execute('products', 'get', { page: 1, limit: 3, storeIds: [1, 2] });
      const product = products.find((product) => product.sku === sku);

      expect(products.length).to.be.equal(3);
      expect(product).not.to.be.undefined;
    });
  });

  context('configurable price should not be 0', function () {
    const requestParams = { page: 1, limit: 100, storeIds: [1, 2] };
    let entityIdUsed;
    let originalPrice;

    let priceTableName = '';

    const setPriceForEntityId = (entityId, value, db, magentoEdition) => {
      const query = magentoEdition === 'Enterprise' ? { row_id: entityId } : { entity_id: entityId };
      return db(priceTableName).where(query).update({ value });
    };

    before(async function () {
      priceTableName = this.getTableName('catalog_product_entity_decimal');

      const { products } = await this.magentoApi.execute('products', 'get', requestParams);
      const configurableProduct = products.find((product) => product.type === 'configurable');
      entityIdUsed = configurableProduct.entity_id;
      originalPrice = configurableProduct.store_data.find((data) => data.store_id !== 0).price;

      await setPriceForEntityId(entityIdUsed, 0, this.db, this.magentoEdition);
      await this.reindex();
    });

    after(async function () {
      await setPriceForEntityId(entityIdUsed, originalPrice, this.db, this.magentoEdition);
      await this.reindex();
    });
    it('returns configurable product min price if price or final price is 0', async function () {
      const { products } = await this.magentoApi.execute('products', 'get', requestParams);
      const configurableProduct = products.find((product) => product.entity_id === entityIdUsed);
      const notAdminStoreData = configurableProduct.store_data.find((data) => data.store_id !== 0);

      expect(notAdminStoreData.price).to.equal(originalPrice);
    });
  });
});

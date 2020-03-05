'use strict';

const localAddresses = {
  shippingAddress: {
    region: 'New York',
    region_id: 43,
    region_code: 'NY',
    country_id: 'US',
    street: ['123 Oak Ave'],
    postcode: '10577',
    city: 'Purchase',
    firstname: 'Jane',
    lastname: 'Doe',
    email: 'jdoe@example.shipping.com',
    telephone: '512-555-1111'
  },
  billingAddress: {
    region: 'New York',
    region_id: 43,
    region_code: 'NY',
    country_id: 'US',
    street: ['123 Oak Ave'],
    postcode: '10577',
    city: 'Purchase',
    firstname: 'Jane',
    lastname: 'Doe',
    email: 'jdoe@example.billing.com',
    telephone: '512-555-1111'
  },
  shipping_carrier_code: 'flatrate',
  shipping_method_code: 'flatrate'
};

const createNewCustomerOrderAndShip = async (magentoApi, localCartItem) => {
  const { data: cartId } = await magentoApi.post({
    path: '/index.php/rest/V1/guest-carts'
  });
  await magentoApi.post({
    path: `/index.php/rest/V1/guest-carts/${cartId}/items`,
    payload: {
      cartItem: { ...localCartItem, quote_id: cartId }
    }
  });
  await magentoApi.post({
    path: `/index.php/rest/V1/guest-carts/${cartId}/shipping-information`,
    payload: {
      addressInformation: localAddresses
    }
  });

  try {
    const { data: orderId } = await magentoApi.put({
      path: `/index.php/rest/V1/guest-carts/${cartId}/order`,
      payload: {
        paymentMethod: {
          method: 'checkmo'
        }
      }
    });

    await magentoApi.post({
      path: `/index.php/rest/V1/order/${orderId}/ship`,
      payload: {
        notify: true
      }
    });

    return { cartId, orderId };
  } catch (error) {
    const util = require('util');
    console.log(`Error during completing ${cartId}, ${error.message}, ${util.inspect(error.response)}`);
  }
};

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

  it('should return product with stock change from order', async function() {
    const localCartItem = this.localCartItem;

    await createNewCustomerOrderAndShip(this.magentoApi, localCartItem);

    const { products: productDeltas } = await this.magentoApi.execute('products', 'getDeltas', {
      page: 1,
      limit: 3,
      storeIds: [1, 2],
      sinceId: 0
    });
    const product = productDeltas.find(product => product.sku === localCartItem.sku);

    expect(productDeltas.length).to.be.equal(1);
    expect(product).not.to.be.undefined;
  });
});

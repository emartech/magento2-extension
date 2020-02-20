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

const createNewCustomerOrder = async (magentoApi, localCartItem) => {
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

    return { cartId, orderId };
  } catch (error) {
    const util = require('util');
    console.log(`Error during completing ${cartId}, ${error.message}, ${util.inspect(error.response)}`);
  }
};

const orderCount = 4;

describe('Orders endpoint', function() {
  let localCartItem;

  before(async function() {
    await this.dbCleaner.clearOrders();
    localCartItem = this.localCartItem;
    await Promise.all([...Array(orderCount)].map(() => createNewCustomerOrder(this.magentoApi, localCartItem)));
  });

  after(async function() {
    await this.dbCleaner.clearOrders();
  });

  it('should return orders and paging info according to parameters', async function() {
    const limit = 1;
    const page = 1;
    const ordersResponse = await this.magentoApi.execute('orders', 'getSinceId', {
      page,
      limit,
      sinceId: 0,
      storeIds: [1]
    });

    expect(ordersResponse.orderCount).to.be.equal(orderCount);
    expect(ordersResponse.orders.length).to.be.equal(limit);
    expect(ordersResponse.lastPage).to.be.equal(orderCount / limit);
    expect(ordersResponse.pageSize).to.be.equal(limit);
    expect(ordersResponse.currentPage).to.be.equal(page);
    expect(ordersResponse.orders[0]).to.have.property('entity_id');
    expect(ordersResponse.orders[0].store_id).to.equal(1);
  });

  it('should handle multiple store IDs', async function() {
    const limit = 1;
    const page = 1;
    const ordersResponse = await this.magentoApi.execute('orders', 'getSinceId', {
      page,
      limit,
      sinceId: 0,
      storeIds: [1, 2]
    });

    expect(ordersResponse.orderCount).to.be.equal(orderCount);
  });

  it('should filter for store IDs', async function() {
    const limit = 1;
    const page = 1;
    const ordersResponse = await this.magentoApi.execute('orders', 'getSinceId', {
      page,
      limit,
      sinceId: 0,
      storeIds: [2]
    });

    expect(ordersResponse.orderCount).to.be.equal(0);
  });

  it('should filter with sinceId', async function() {
    const limit = 1;
    const page = 2;
    const sinceId = 2;
    const ordersResponse = await this.magentoApi.execute('orders', 'getSinceId', {
      page,
      limit,
      sinceId,
      storeIds: [1]
    });

    expect(ordersResponse.orderCount).to.be.equal(orderCount - sinceId);
    expect(ordersResponse.lastPage).to.be.equal((orderCount - sinceId) / limit);
    expect(ordersResponse.currentPage).to.be.equal(page);
  });
});

'use strict';

const localCartItem = {
  sku: 'WS03',
  qty: 1,
  product_type: 'configurable',
  product_option: {
    extension_attributes: {
      configurable_item_options: [
        {
          option_id: 93,
          option_value: 50
        },
        {
          option_id: 145,
          option_value: 167
        }
      ]
    }
  }
};

const localAddresses = {
  shipping_address: {
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
  billing_address: {
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

const createNewCustomerOrder = async (magentoApi, customer) => {
  const { data: cartId } = await magentoApi.post({
    path: `/index.php/rest/V1/customers/${customer.entityId}/carts`
  });

  await magentoApi.post({
    path: `/index.php/rest/V1/carts/${cartId}/items`,
    payload: {
      cartItem: Object.assign(localCartItem, { quote_id: cartId })
    }
  });

  await magentoApi.post({
    path: `/index.php/rest/V1/carts/${cartId}/shipping-information`,
    payload: {
      addressInformation: localAddresses
    }
  });

  const { data: orderId } = await magentoApi.put({
    path: `/index.php/rest/V1/carts/${cartId}/order`,
    payload: {
      paymentMethod: {
        method: 'checkmo'
      }
    }
  });

  return { cartId, orderId };
};

const orderCount = 10;

describe('Orders endpoint', function() {
  before(async function() {
    await this.dbCleaner.clearOrders();
    for (let orderNumber = 0; orderCount > orderNumber; orderNumber++) {
      await createNewCustomerOrder(this.magentoApi, this.customer);
    }
  });

  after(async function() {
    await this.dbCleaner.clearOrders();
  });

  it('should return orders and paging info according to parameters', async function() {
    const limit = 2;
    const page = 1;
    const ordersResponse = await this.magentoApi.execute('orders', 'getSinceId', { page, limit, sinceId: 0 });

    expect(ordersResponse.orderCount).to.be.equal(orderCount);
    expect(ordersResponse.orders.length).to.be.equal(limit);
    expect(ordersResponse.lastPage).to.be.equal(orderCount / limit);
    expect(ordersResponse.pageSize).to.be.equal(limit);
    expect(ordersResponse.currentPage).to.be.equal(page);
    expect(ordersResponse.orders[0]).to.have.property('entity_id');
  });

  it('should filter with sinceId', async function() {
    const limit = 2;
    const page = 2;
    const sinceId = 4;
    const ordersResponse = await this.magentoApi.execute('orders', 'getSinceId', { page, limit, sinceId });

    expect(ordersResponse.orderCount).to.be.equal(orderCount - sinceId);
    expect(ordersResponse.lastPage).to.be.equal((orderCount - sinceId) / limit);
    expect(ordersResponse.currentPage).to.be.equal(page);
  });
});

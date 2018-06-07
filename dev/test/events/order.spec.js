'use strict';

const getLastEvent = async db =>
  await db
    .select()
    .from('emarsys_events')
    .orderBy('event_id', 'desc')
    .first();

const createNewOrder = async (magentoApi, customer, product) => {
  const { data: cartId } = await magentoApi.post({
    path: `/index.php/rest/V1/customers/${customer.entityId}/carts`
  });

  await magentoApi.post({
    path: `/index.php/rest/V1/carts/${cartId}/items`,
    payload: {
      cartItem: {
        sku: product.sku,
        qty: 1,
        quote_id: `${cartId}`
      }
    }
  });

  await magentoApi.post({
    path: `/index.php/rest/V1/carts/${cartId}/shipping-information`,
    payload: {
      addressInformation: {
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
          email: 'jdoe@example.com',
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
          email: 'jdoe@example.com',
          telephone: '512-555-1111'
        },
        shipping_carrier_code: 'flatrate',
        shipping_method_code: 'flatrate'
      }
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

const fulfillOrder = async (magentoApi, orderId) => {
  await magentoApi.post({
    path: `/index.php/rest/V1/order/${orderId}/invoice`,
    payload: {
      capture: true
    }
  });

  await magentoApi.post({
    path: `/index.php/rest/V1/order/${orderId}/ship`
  });
};

const cancelOrder = async (magentoApi, orderId) => {
  await magentoApi.post({
    path: `/index.php/rest/V1/orders/${orderId}/cancel`
  });
};

describe('Order events', function() {
  it('creates orders/new event and an orders/fulfilled', async function() {
    await this.magentoApi.setSettings({ collectSalesEvents: 'enabled' });

    const { orderId } = await createNewOrder(this.magentoApi, this.customer, this.product);

    const { event_type: createEventType } = await getLastEvent(this.db);

    expect(createEventType).to.be.equal('orders/create');

    await fulfillOrder(this.magentoApi, orderId);

    const { event_type: fulfillEventType } = await getLastEvent(this.db);

    expect(fulfillEventType).to.be.equal('orders/fulfilled');
  });

  it('should not create events until disabled and after enable should create an orders/cancelled', async function() {
    const { orderId } = await createNewOrder(this.magentoApi, this.customer, this.product);

    const createEvent = await getLastEvent(this.db);

    expect(createEvent).to.be.undefined;

    await this.magentoApi.setSettings({ collectSalesEvents: 'enabled' });

    await cancelOrder(this.magentoApi, orderId);

    const { event_type: cancelEventType } = await getLastEvent(this.db);

    expect(cancelEventType).to.be.equal('orders/cancelled');
  });
});

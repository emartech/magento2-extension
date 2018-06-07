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
        shipping_carrier_code: 'tablerate',
        shipping_method_code: 'bestway'
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

describe('Order events', function() {
  it('creates orders/new event', async function() {
    await this.magentoApi.setSettings({ collectSalesEvents: 'enabled' });

    await createNewOrder(this.magentoApi, this.customer, this.product);

    const { event_type: eventType } = await getLastEvent(this.db);

    expect(eventType).to.be.equal('orders/create');
  });
});

'use strict';

const getLastEvent = async db =>
  await db
    .select()
    .from('emarsys_events_data')
    .orderBy('event_id', 'desc')
    .first();

const createNewOrder = async (magentoApi, customer) => {
  const { data: cartId } = await magentoApi.post({
    path: `/index.php/rest/V1/customers/${customer.entityId}/carts`
  });

  await magentoApi.post({
    path: `/index.php/rest/V1/carts/${cartId}/items`,
    payload: {
      cartItem: {
        sku: 'WS03',
        qty: 1,
        product_type: 'configurable',
        quote_id: cartId,
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
  context('setting enabled', function() {
    before(async function() {
      await this.magentoApi.setConfig({
        websiteId: 1,
        config: { collectSalesEvents: 'enabled' }
      });
    });

    it('should create orders/new event and an orders/fulfilled', async function() {
      const { orderId } = await createNewOrder(this.magentoApi, this.customer);

      const { event_type: createEventType, event_data: createEventPayload } = await getLastEvent(this.db);
      const createdEventData = JSON.parse(createEventPayload);
      const simpleItem = createdEventData.items[1];

      expect(createEventType).to.be.equal('orders/create');
      expect(simpleItem.parent_item).not.to.be.empty;
      expect(createdEventData.addresses).to.have.property('shipping');
      expect(createdEventData.addresses).to.have.property('billing');

      await fulfillOrder(this.magentoApi, orderId);

      const { event_type: fulfillEventType } = await getLastEvent(this.db);

      expect(fulfillEventType).to.be.equal('orders/fulfilled');
    });

    it('should create orders/cancelled event', async function() {
      const { orderId } = await createNewOrder(this.magentoApi, this.customer);
      await cancelOrder(this.magentoApi, orderId);

      const { event_type: cancelEventType } = await getLastEvent(this.db);

      expect(cancelEventType).to.be.equal('orders/cancelled');
    });

    context('store is not enabled', function() {
      before(async function() {
        await this.clearStoreSettings();
      });

      after(async function() {
        await this.setDefaultStoreSettings();
      });

      it('should not create event', async function() {
        await this.magentoApi.setDefaultConfig(1);

        await createNewOrder(this.magentoApi, this.customer, this.product);
        const createEvent = await getLastEvent(this.db);

        expect(createEvent).to.be.undefined;
      });
    });
  });

  context('setting disabled', function() {
    it('should not create event', async function() {
      await this.magentoApi.setDefaultConfig(1);

      await createNewOrder(this.magentoApi, this.customer, this.product);
      const createEvent = await getLastEvent(this.db);

      expect(createEvent).to.be.undefined;
    });
  });
});

'use strict';

const getLastEvent = async db =>
  await db
    .select()
    .from('emarsys_events_data')
    .orderBy('event_id', 'desc')
    .first();

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

const createNewGuestOrder = async magentoApi => {
  const { data: cartId } = await magentoApi.post({
    path: '/index.php/rest/V1/guest-carts'
  });

  const { data: item } = await magentoApi.post({
    path: `/index.php/rest/V1/guest-carts/${cartId}/items`,
    payload: {
      cartItem: Object.assign(localCartItem, { quote_id: cartId })
    }
  });
  const quoteId = item.quote_id;

  await magentoApi.post({
    path: `/index.php/rest/V1/carts/${quoteId}/shipping-information`,
    payload: {
      addressInformation: localAddresses
    }
  });

  const { data: orderId } = await magentoApi.put({
    path: `/index.php/rest/V1/guest-carts/${cartId}/order`,
    payload: {
      paymentMethod: {
        method: 'checkmo'
      }
    }
  });

  return { cartId, orderId };
};

const invoiceOrder = async (magentoApi, orderId) => {
  await magentoApi.post({
    path: `/index.php/rest/V1/order/${orderId}/invoice`,
    payload: {
      capture: true,
      notify: true
    }
  });
};

const shipOrder = async (magentoApi, orderId) => {
  await magentoApi.post({
    path: `/index.php/rest/V1/order/${orderId}/ship`,
    payload: {
      notify: true
    }
  });
};

const commentOrder = async (magentoApi, orderId) => {
  await magentoApi.post({
    path: `/index.php/rest/V1/orders/${orderId}/comments`,
    payload: {
      statusHistory: {
        comment: 'Comment on order',
        is_customer_notified: 1
      }
    }
  });
};

const refundOrder = async (magentoApi, orderId) => {
  await magentoApi.post({
    path: `/index.php/rest/V1/order/${orderId}/refund`,
    payload: {
      notify: true
    }
  });
};

const expectCustomerMatches = function(createdEventData, customer) {
  expect(createdEventData.customer).to.containSubset({
    email: customer.email,
    firstname: customer.firstname,
    lastname: customer.lastname,
    entityId: customer.id
  });
};

const expectOrderMatches = function(createdEventData) {
  const orderItem = createdEventData.order.items[0];
  expect(orderItem.sku).to.contain(localCartItem.sku);
  expect(createdEventData.order.addresses).to.have.property('shipping');
  expect(createdEventData.order.addresses).to.have.property('billing');
};

const expectCustomerAndOrderMatches = function(createdEventData, customer) {
  expectCustomerMatches(createdEventData, customer);
  expectOrderMatches(createdEventData);
};

describe('Marketing events: sales', function() {
  after(async function() {
    await this.magentoApi.execute('config', 'set', {
      websiteId: 1,
      config: { collectMarketingEvents: 'disabled' }
    });
    await this.db.truncate('emarsys_events_data');
  });

  describe('If config collectMarketingEvents is disabled', function() {
    before(async function() {
      await this.magentoApi.execute('config', 'set', {
        websiteId: 1,
        config: {
          collectCustomerEvents: 'disabled',
          collectSalesEvents: 'disabled',
          collectMarketingEvents: 'disabled'
        }
      });
    });

    it('should not create event', async function() {
      await createNewCustomerOrder(this.magentoApi, this.customer);

      const createdEvent = await getLastEvent(this.db);

      expect(createdEvent).to.be.undefined;
    });
  });

  describe('If config collectMarketingEvents is enabled', function() {
    before(async function() {
      await this.magentoApi.execute('config', 'set', {
        websiteId: 1,
        config: { collectMarketingEvents: 'enabled' }
      });
    });

    describe('when customer', function() {
      let orderId;
      before(async function() {
        const order = await createNewCustomerOrder(this.magentoApi, this.customer);
        orderId = order.orderId;
      });

      describe('submits order', function() {
        it('should create sales_email_order_template event', async function() {
          const event = await getLastEvent(this.db);
          const createdEventData = JSON.parse(event.event_data);

          expect(event.event_type).to.equal('sales_email_order_template');
          expectCustomerAndOrderMatches(createdEventData, this.customer);
          expect(createdEventData.order.addresses).to.have.property('billing');
          expect(event.entity_id).to.equal(parseInt(orderId));
          expect(event.website_id).to.equal(1);
          expect(event.store_id).to.equal(1);
        });
      });

      describe('order is invoiced', function() {
        it('should create sales_email_invoice_template event', async function() {
          await invoiceOrder(this.magentoApi, orderId);

          const event = await getLastEvent(this.db);
          const createdEventData = JSON.parse(event.event_data);
          expect(event.event_type).to.equal('sales_email_invoice_template');
          expectCustomerAndOrderMatches(createdEventData, this.customer);
          expect(createdEventData.invoice.order_id).to.equal(orderId);
          expect(event.website_id).to.equal(1);
          expect(event.store_id).to.equal(1);
        });
      });

      describe('order is shipped', function() {
        it('should create sales_email_shipment_template event', async function() {
          await shipOrder(this.magentoApi, orderId);

          const event = await getLastEvent(this.db);
          const createdEventData = JSON.parse(event.event_data);

          expect(event.event_type).to.equal('sales_email_shipment_template');
          expectCustomerAndOrderMatches(createdEventData, this.customer);
          expect(createdEventData.order.shipments[0].order_id).to.equal(orderId);
          expect(event.website_id).to.equal(1);
          expect(event.store_id).to.equal(1);
        });
      });

      describe('order is', function() {
        let orderId;

        before(async function() {
          const order = await createNewCustomerOrder(this.magentoApi, this.customer);
          orderId = order.orderId;
        });
        describe('commented', function() {
          it('should create sales_email_order_comment_template event', async function() {
            await commentOrder(this.magentoApi, orderId);

            const event = await getLastEvent(this.db);
            const createdEventData = JSON.parse(event.event_data);

            expect(event.event_type).to.equal('sales_email_order_comment_template');
            expectCustomerAndOrderMatches(createdEventData, this.customer);
            expect(createdEventData.order.comments[0].comment).to.equal('Comment on order');
            expect(event.website_id).to.equal(1);
            expect(event.store_id).to.equal(1);
          });
        });
        describe('refunded', function() {
          it('should create sales_email_creditmemo_template event', async function() {
            await invoiceOrder(this.magentoApi, orderId);
            await refundOrder(this.magentoApi, orderId);

            const event = await getLastEvent(this.db);
            const createdEventData = JSON.parse(event.event_data);

            expect(event.event_type).to.equal('sales_email_creditmemo_template');
            expectCustomerAndOrderMatches(createdEventData, this.customer);
            expect(createdEventData.creditmemo.order_id).to.equal(orderId);
            expect(event.website_id).to.equal(1);
            expect(event.store_id).to.equal(1);
          });
        });
      });

      describe('store is not enabled', function() {
        before(async function() {
          await this.clearStoreSettings();
          await this.db.truncate('emarsys_events_data');
        });

        after(async function() {
          await this.setDefaultStoreSettings();
        });

        it('should not create event', async function() {
          await createNewCustomerOrder(this.magentoApi, this.customer);

          const createdEvent = await getLastEvent(this.db);

          expect(createdEvent).to.be.undefined;
        });
      });
    });

    describe('when guest', function() {
      let orderId;
      before(async function() {
        const order = await createNewGuestOrder(this.magentoApi, this.customer);
        orderId = order.orderId;
      });

      describe('submits order', function() {
        it('should create sales_email_order_guest_template event', async function() {
          const event = await getLastEvent(this.db);
          const createdEventData = JSON.parse(event.event_data);

          expect(event.event_type).to.equal('sales_email_order_guest_template');
          expectOrderMatches(createdEventData);
          expect(createdEventData.order.addresses.billing.email).to.equal(localAddresses.billing_address.email);
          expect(event.website_id).to.equal(1);
          expect(event.store_id).to.equal(1);
        });
      });

      describe('order is invoiced', function() {
        it('should create sales_email_invoice_guest_template event', async function() {
          await invoiceOrder(this.magentoApi, orderId);

          const event = await getLastEvent(this.db);
          const createdEventData = JSON.parse(event.event_data);

          expect(event.event_type).to.equal('sales_email_invoice_guest_template');
          expect(createdEventData.customerEmail).to.equal(localAddresses.billing_address.email);
          expectOrderMatches(createdEventData);
          expect(createdEventData.invoice.order_id).to.equal(orderId);
          expect(event.website_id).to.equal(1);
          expect(event.store_id).to.equal(1);
        });
      });

      describe('order is shipped', function() {
        it('should create sales_email_shipment_guest_template event', async function() {
          await shipOrder(this.magentoApi, orderId);

          const event = await getLastEvent(this.db);
          const createdEventData = JSON.parse(event.event_data);

          expect(event.event_type).to.equal('sales_email_shipment_guest_template');
          expectOrderMatches(createdEventData, this.customer);
          expect(createdEventData.order.shipments[0].order_id).to.equal(orderId);
          expect(event.website_id).to.equal(1);
          expect(event.store_id).to.equal(1);
        });
      });

      describe('order is', function() {
        let orderId;
        before(async function() {
          const order = await createNewGuestOrder(this.magentoApi, this.customer);
          orderId = order.orderId;
        });

        describe('commented', function() {
          it('should create sales_email_order_comment_guest_template event', async function() {
            await commentOrder(this.magentoApi, orderId);

            const event = await getLastEvent(this.db);
            const createdEventData = JSON.parse(event.event_data);

            expect(event.event_type).to.equal('sales_email_order_comment_guest_template');
            expectOrderMatches(createdEventData, this.customer);
            expect(createdEventData.order.comments[0].comment).to.equal('Comment on order');
            expect(event.website_id).to.equal(1);
            expect(event.store_id).to.equal(1);
          });
        });
        describe('refunded', function() {
          it('should create sales_email_creditmemo_guest_template event', async function() {
            await invoiceOrder(this.magentoApi, orderId);
            await refundOrder(this.magentoApi, orderId);

            const event = await getLastEvent(this.db);
            const createdEventData = JSON.parse(event.event_data);

            expect(event.event_type).to.equal('sales_email_creditmemo_guest_template');
            expectOrderMatches(createdEventData, this.customer);
            expect(createdEventData.creditmemo.order_id).to.equal(orderId);
            expect(event.website_id).to.equal(1);
            expect(event.store_id).to.equal(1);
          });
        });
      });
    });
  });
});

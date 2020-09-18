'use strict';

const {
  createNewCustomerOrder,
  createNewGuestOrder,
  invoiceOrder,
  localAddresses,
  shipOrder,
  commentOrder,
  refundOrder
} = require('../helpers/orders');

const getLastEvent = async (db) =>
  await db.select().from(`${tablePrefix}emarsys_events_data`).orderBy('event_id', 'desc').first();

const expectCustomerMatches = function (createdEventData, customer) {
  expect(createdEventData.customer).to.containSubset({
    email: customer.email,
    firstname: customer.firstname,
    lastname: customer.lastname,
    entityId: customer.id,
    extra_fields: [
      {
        key: customer.custom_attributes[0].attribute_code,
        value: customer.custom_attributes[0].value
      }
    ]
  });
};

const expectOrderMatches = function (createdEventData, localCartItem) {
  const orderItem = createdEventData.order.items[0];
  expect(orderItem.sku).to.contain(localCartItem.sku);
  expect(createdEventData.order.addresses).to.have.property('shipping');
  expect(createdEventData.order.addresses).to.have.property('billing');
};

const expectCustomerAndOrderMatches = function (createdEventData, customer, localCartItem) {
  expectCustomerMatches(createdEventData, customer);
  expectOrderMatches(createdEventData, localCartItem);
};

let tablePrefix;

describe('Marketing events: sales', function () {
  let localCartItem;
  before(function () {
    tablePrefix = this.getTableName('');
    localCartItem = this.localCartItem;
  });

  after(async function () {
    await this.magentoApi.execute('config', 'set', {
      websiteId: 1,
      config: { collectMarketingEvents: 'disabled' }
    });
    await this.magentoApi.execute('attributes', 'set', {
      websiteId: 1,
      type: 'customer',
      attributeCodes: []
    });
    await this.db.truncate(this.getTableName('emarsys_events_data'));
  });

  describe('If config collectMarketingEvents is disabled', function () {
    before(async function () {
      await this.magentoApi.execute('config', 'set', {
        websiteId: 1,
        config: {
          collectCustomerEvents: 'disabled',
          collectSalesEvents: 'disabled',
          collectMarketingEvents: 'disabled'
        }
      });
    });

    it('should not create event', async function () {
      await createNewCustomerOrder(this.magentoApi, this.customer, localCartItem);

      const createdEvent = await getLastEvent(this.db);

      expect(createdEvent).to.be.undefined;
    });
  });

  describe('If config collectMarketingEvents is enabled', function () {
    before(async function () {
      await this.magentoApi.execute('config', 'set', {
        websiteId: 1,
        config: { collectMarketingEvents: 'enabled' }
      });
      await this.magentoApi.execute('attributes', 'set', {
        websiteId: 1,
        type: 'customer',
        attributeCodes: ['emarsys_test_favorite_car']
      });
    });

    describe('when customer', function () {
      let orderId;
      before(async function () {
        const order = await createNewCustomerOrder(this.magentoApi, this.customer, localCartItem);
        orderId = order.orderId;
      });

      describe('submits order', function () {
        it('should create sales_email_order_template event', async function () {
          const event = await getLastEvent(this.db);
          const createdEventData = JSON.parse(event.event_data);

          expect(event.event_type).to.equal('sales_email_order_template');
          expectCustomerAndOrderMatches(createdEventData, this.customer, localCartItem);
          expect(createdEventData.order.addresses).to.have.property('billing');
          expect(event.entity_id).to.equal(parseInt(orderId));
          expect(event.website_id).to.equal(1);
          expect(event.store_id).to.equal(1);
        });
      });

      describe('order is invoiced', function () {
        it('should create sales_email_invoice_template event', async function () {
          await invoiceOrder(this.magentoApi, orderId);

          const event = await getLastEvent(this.db);
          const createdEventData = JSON.parse(event.event_data);
          expect(event.event_type).to.equal('sales_email_invoice_template');
          expectCustomerAndOrderMatches(createdEventData, this.customer, localCartItem);
          expect(createdEventData.invoice.order_id).to.equal(orderId);
          expect(event.website_id).to.equal(1);
          expect(event.store_id).to.equal(1);
        });
      });

      describe('order is shipped', function () {
        it('should create sales_email_shipment_template event', async function () {
          await shipOrder(this.magentoApi, orderId);

          const event = await getLastEvent(this.db);
          const createdEventData = JSON.parse(event.event_data);

          expect(event.event_type).to.equal('sales_email_shipment_template');
          expectCustomerAndOrderMatches(createdEventData, this.customer, localCartItem);
          expect(createdEventData.order.shipments[0].order_id).to.equal(orderId);
          expect(event.website_id).to.equal(1);
          expect(event.store_id).to.equal(1);
        });
      });

      describe('order is', function () {
        let orderId;

        before(async function () {
          const order = await createNewCustomerOrder(this.magentoApi, this.customer, localCartItem);
          orderId = order.orderId;
        });
        describe('commented', function () {
          it('should create sales_email_order_comment_template event', async function () {
            await commentOrder(this.magentoApi, orderId);

            const event = await getLastEvent(this.db);
            const createdEventData = JSON.parse(event.event_data);

            expect(event.event_type).to.equal('sales_email_order_comment_template');
            expectCustomerAndOrderMatches(createdEventData, this.customer, localCartItem);
            expect(createdEventData.order.comments[0].comment).to.equal('Comment on order');
            expect(event.website_id).to.equal(1);
            expect(event.store_id).to.equal(1);
          });
        });
        describe('refunded', function () {
          it('should create sales_email_creditmemo_template event', async function () {
            await invoiceOrder(this.magentoApi, orderId);
            await refundOrder(this.magentoApi, orderId);

            const event = await getLastEvent(this.db);
            const createdEventData = JSON.parse(event.event_data);

            expect(event.event_type).to.equal('sales_email_creditmemo_template');
            expectCustomerAndOrderMatches(createdEventData, this.customer, localCartItem);
            expect(createdEventData.creditmemo.order_id).to.equal(orderId);
            expect(event.website_id).to.equal(1);
            expect(event.store_id).to.equal(1);
          });
        });
      });

      describe('store is not enabled', function () {
        before(async function () {
          await this.clearStoreSettings();
          await this.db.truncate(this.getTableName('emarsys_events_data'));
        });

        after(async function () {
          await this.setDefaultStoreSettings();
        });

        it('should not create event', async function () {
          await createNewCustomerOrder(this.magentoApi, this.customer, localCartItem);

          const createdEvent = await getLastEvent(this.db);

          expect(createdEvent).to.be.undefined;
        });
      });
    });

    describe('when guest', function () {
      let orderId;
      before(async function () {
        const order = await createNewGuestOrder(this.magentoApi, localCartItem);
        orderId = order.orderId;
      });

      describe('submits order', function () {
        it('should create sales_email_order_guest_template event', async function () {
          const event = await getLastEvent(this.db);
          const createdEventData = JSON.parse(event.event_data);

          expect(event.event_type).to.equal('sales_email_order_guest_template');
          expectOrderMatches(createdEventData, localCartItem);
          expect(createdEventData.order.addresses.billing.email).to.equal(
            localAddresses.billing_address.email
          );
          expect(event.website_id).to.equal(1);
          expect(event.store_id).to.equal(1);
        });
      });

      describe('order is invoiced', function () {
        it('should create sales_email_invoice_guest_template event', async function () {
          await invoiceOrder(this.magentoApi, orderId);

          const event = await getLastEvent(this.db);
          const createdEventData = JSON.parse(event.event_data);

          expect(event.event_type).to.equal('sales_email_invoice_guest_template');
          expect(createdEventData.customerEmail).to.equal(localAddresses.billing_address.email);
          expectOrderMatches(createdEventData, localCartItem);
          expect(createdEventData.invoice.order_id).to.equal(orderId);
          expect(event.website_id).to.equal(1);
          expect(event.store_id).to.equal(1);
        });
      });

      describe('order is shipped', function () {
        it('should create sales_email_shipment_guest_template event', async function () {
          await shipOrder(this.magentoApi, orderId);

          const event = await getLastEvent(this.db);
          const createdEventData = JSON.parse(event.event_data);

          expect(event.event_type).to.equal('sales_email_shipment_guest_template');
          expectOrderMatches(createdEventData, localCartItem);
          expect(createdEventData.order.shipments[0].order_id).to.equal(orderId);
          expect(event.website_id).to.equal(1);
          expect(event.store_id).to.equal(1);
        });
      });

      describe('order is', function () {
        let orderId;
        before(async function () {
          const order = await createNewGuestOrder(this.magentoApi, localCartItem);
          orderId = order.orderId;
        });

        describe('commented', function () {
          it('should create sales_email_order_comment_guest_template event', async function () {
            await commentOrder(this.magentoApi, orderId);

            const event = await getLastEvent(this.db);
            const createdEventData = JSON.parse(event.event_data);

            expect(event.event_type).to.equal('sales_email_order_comment_guest_template');
            expectOrderMatches(createdEventData, localCartItem);
            expect(createdEventData.order.comments[0].comment).to.equal('Comment on order');
            expect(event.website_id).to.equal(1);
            expect(event.store_id).to.equal(1);
          });
        });
        describe('refunded', function () {
          it('should create sales_email_creditmemo_guest_template event', async function () {
            await invoiceOrder(this.magentoApi, orderId);
            await refundOrder(this.magentoApi, orderId);

            const event = await getLastEvent(this.db);
            const createdEventData = JSON.parse(event.event_data);

            expect(event.event_type).to.equal('sales_email_creditmemo_guest_template');
            expectOrderMatches(createdEventData, localCartItem);
            expect(createdEventData.creditmemo.order_id).to.equal(orderId);
            expect(event.website_id).to.equal(1);
            expect(event.store_id).to.equal(1);
          });
        });
      });
    });
  });
});

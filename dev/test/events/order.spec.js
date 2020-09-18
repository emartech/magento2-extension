'use strict';

const {
  createNewCustomerOrder,
  refundOnePieceFromFirstItemOfOrder,
  fulfillOrder,
  changeOrderStatus
} = require('../helpers/orders');

const getLastEvent = async (db) =>
  await db.select().from(`${tablePrefix}emarsys_events_data`).orderBy('event_id', 'desc').first();

const getAllEvents = async (db) =>
  await db.select().from(`${tablePrefix}emarsys_events_data`).orderBy('event_id', 'desc');

let tablePrefix;

describe('Order events', function () {
  let localCartItem;
  before(function () {
    tablePrefix = this.getTableName('');
    localCartItem = this.localCartItem;
  });
  context('setting enabled', function () {
    before(async function () {
      await this.magentoApi.execute('config', 'set', {
        websiteId: 1,
        config: { collectSalesEvents: 'enabled', collectMarketingEvents: 'disabled', magentoSendEmail: 'disabled' }
      });
    });

    it('should create orders/new event and an orders/fulfilled', async function () {
      const { orderId } = await createNewCustomerOrder(this.magentoApi, this.customer, localCartItem);

      await fulfillOrder(this.magentoApi, orderId);

      const { event_type: fulfillEventType } = await getLastEvent(this.db);

      expect(fulfillEventType).to.be.equal('orders/fulfilled');
    });

    it('should not log orders/fulfilled when an order re-enters the complete state', async function () {
      const { orderId } = await createNewCustomerOrder(this.magentoApi, this.customer, localCartItem);
      await fulfillOrder(this.magentoApi, orderId);
      await changeOrderStatus(this.magentoApi, orderId, 'test_status', 'complete');

      const events = await getAllEvents(this.db);
      const fulfilledOrders = events.filter((event) => event.event_type === 'orders/fulfilled');

      expect(fulfilledOrders.length).to.be.equal(1);

      const eventData = JSON.parse(fulfilledOrders[0].event_data);
      expect(eventData.status).to.be.equal('complete');
    });

    it('should not log orders/fulfilled when a partial refund occurs', async function () {
      const { orderId } = await createNewCustomerOrder(this.magentoApi, this.customer, localCartItem);
      await fulfillOrder(this.magentoApi, orderId);

      await this.dbCleaner.resetEmarsysEventsData();

      await refundOnePieceFromFirstItemOfOrder(this.magentoApi, orderId);

      const events = await getAllEvents(this.db);

      const fulfilledOrders = events.filter((event) => event.event_type === 'orders/fulfilled');
      expect(fulfilledOrders.length).to.be.equal(0);

      const refundEvents = events.filter((event) => event.event_type === 'refunds/fulfilled');
      expect(refundEvents.length).to.be.equal(1);
      const refundData = JSON.parse(refundEvents[0].event_data);
      expect(refundData.order_id).to.be.equal(orderId);
    });

    it('should not group refunds/fulfilled events', async function () {
      const { orderId } = await createNewCustomerOrder(this.magentoApi, this.customer, localCartItem);
      await fulfillOrder(this.magentoApi, orderId);

      await refundOnePieceFromFirstItemOfOrder(this.magentoApi, orderId);
      await refundOnePieceFromFirstItemOfOrder(this.magentoApi, orderId);

      const events = await getAllEvents(this.db);
      const refundEvents = events.filter((event) => event.event_type === 'refunds/fulfilled');

      expect(refundEvents.length).to.be.equal(2);
    });

    context('store is not enabled', function () {
      before(async function () {
        await this.clearStoreSettings();
      });

      after(async function () {
        await this.setDefaultStoreSettings();
      });

      it('should not create event', async function () {
        await this.turnOffEverySetting(1);

        await createNewCustomerOrder(this.magentoApi, this.customer, localCartItem);
        const createEvent = await getLastEvent(this.db);

        expect(createEvent).to.be.undefined;
      });
    });
  });

  context('setting disabled', function () {
    it('should not create event', async function () {
      await this.turnOffEverySetting(1);
      await createNewCustomerOrder(this.magentoApi, this.customer, localCartItem);

      const createEvent = await getLastEvent(this.db);

      expect(createEvent).to.be.undefined;
    });
  });
});

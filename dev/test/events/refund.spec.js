'use strict';

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

const getLastEvent = async (db) =>
  await db.select().from(`${tablePrefix}emarsys_events_data`).orderBy('event_id', 'desc').first();

const createNewCustomerOrder = async (magentoApi, customer, localCartItem) => {
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

const createNewGuestOrder = async (magentoApi, localCartItem) => {
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

const refundOnePieceFromFirstItemOfOrder = async (magentoApi, orderId) => {
  const { items } = await magentoApi.get({ path: `/index.php/rest/V1/orders/${orderId}` });
  const itemId = items[0].item_id;
  const refundedSku = items[0].sku;

  await magentoApi.post({
    path: `/index.php/rest/V1/order/${orderId}/refund`,
    payload: {
      items: [
        {
          order_item_id: itemId,
          qty: 1
        }
      ],
      notify: false
    }
  });

  return refundedSku;
};

const expectEventDataWithCustomer = (eventData, customer) => {
  expect(eventData.customer_id).to.contain(customer.entityId);
  expect(eventData.customer_email).to.equal(customer.email);
};

const expectEventDataWithGuestCustomer = (eventData) => {
  expect(eventData.customer_id).to.be.null;
  expect(eventData.customer_is_guest).to.be.equal('1');
  expect(eventData.customer_email).to.be.equal(localAddresses.billing_address.email);
};

const expectEventDataWithOneItemRefund = (eventData, refundedSku) => {
  const orderItems = eventData.items.filter((item) => item.product_type === 'configurable' && item.sku === refundedSku);
  expect(orderItems.length).to.be.equal(1);

  const notRefundedOrderItems = eventData.items.filter(
    (item) => item.sku !== refundedSku
  );
  expect(notRefundedOrderItems.length).to.be.equal(0);

  const orderItem = orderItems[0];
  expect(orderItem.sku).to.equal(refundedSku);
  expect(orderItem.qty).to.be.equal(1);
  expect(eventData.addresses).to.have.property('shipping');
  expect(eventData.addresses).to.have.property('billing');
};

let tablePrefix;

describe('Refund events', function () {
  before(async function () {
    tablePrefix = this.getTableName('');
  });

  context('collectSalesEvents is DISABLED', () => {
    before(async function () {
      await this.magentoApi.execute('config', 'set', {
        websiteId: 1,
        config: { collectSalesEvents: 'disabled' }
      });
    });

    it('should create refund event for guest order', async function () {
      const order = await createNewGuestOrder(this.magentoApi, this.localCartItem);
      const orderId = order.orderId;
      await invoiceOrder(this.magentoApi, orderId);
      await refundOnePieceFromFirstItemOfOrder(this.magentoApi, orderId);

      const event = await getLastEvent(this.db);

      expect(event).to.be.undefined;
    });
  });

  context('collectSalesEvents is ENABLED', function () {
    before(async function () {
      await this.magentoApi.execute('config', 'set', {
        websiteId: 1,
        config: { collectSalesEvents: 'enabled' }
      });
    });

    it('should create refund event for non-guest order', async function () {
      const { orderId } = await createNewCustomerOrder(this.magentoApi, this.customer, this.localCartItem);
      await invoiceOrder(this.magentoApi, orderId);
      const refundedSku = await refundOnePieceFromFirstItemOfOrder(this.magentoApi, orderId);

      const event = await getLastEvent(this.db);
      const createdEventData = JSON.parse(event.event_data);

      expect(event.event_type).to.equal('refunds/fulfilled');
      expectEventDataWithCustomer(createdEventData, this.customer);
      expectEventDataWithOneItemRefund(createdEventData, refundedSku);
      expect(createdEventData.order_id).to.equal(orderId);
      expect(event.website_id).to.equal(1);
      expect(event.store_id).to.equal(1);
    });

    it('should create refund event for guest order', async function () {
      const { orderId } = await createNewGuestOrder(this.magentoApi, this.localCartItem);
      await invoiceOrder(this.magentoApi, orderId);
      const refundedSku = await refundOnePieceFromFirstItemOfOrder(this.magentoApi, orderId);

      const event = await getLastEvent(this.db);
      const createdEventData = JSON.parse(event.event_data);

      expect(event.event_type).to.equal('refunds/fulfilled');
      expectEventDataWithGuestCustomer(createdEventData);
      expectEventDataWithOneItemRefund(createdEventData, refundedSku);
      expect(createdEventData.order_id).to.equal(orderId);
      expect(event.website_id).to.equal(1);
      expect(event.store_id).to.equal(1);
    });

    it('should ensure that parent items are referenced by the child items parent_item', async function () {
      const { orderId } = await createNewGuestOrder(this.magentoApi, this.localCartItem);
      await invoiceOrder(this.magentoApi, orderId);
      await refundOnePieceFromFirstItemOfOrder(this.magentoApi, orderId);

      const event = await getLastEvent(this.db);
      const createdEventData = JSON.parse(event.event_data);
      expect(createdEventData.items.length).to.be.equal(2);

      const simpleItem = createdEventData.items.find(item => item.product_type === 'simple');
      const configurableItem = createdEventData.items.find(item => item.product_type === 'configurable');

      expect(simpleItem.parent_item.order_item_id).to.be.equal(configurableItem.order_item_id);
    });
  });
});

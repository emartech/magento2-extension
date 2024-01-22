'use strict';

const { shipOrder, createNewGuestOrder } = require('../helpers/orders');

const orderCount = 4;

describe('Orders endpoint', function() {
  let localCartItem;

  before(async function() {
    await this.dbCleaner.clearOrders();
    localCartItem = this.localCartItem;

    for (let index = 0; index < orderCount; index++) {
      const { orderId } = await createNewGuestOrder(this.magentoApi, localCartItem);
      await shipOrder(this.magentoApi, orderId);
    }
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

'use strict';

const {shipOrder, createNewGuestOrder} = require('../helpers/orders');

const orderCount = 4;

describe('Orders endpoint', function () {
    let localCartItem;

    const buildApiPath = (basePath, params = {}) => {
        const query = new URLSearchParams(params).toString();
        return `${basePath}?${query}`;
    };

    const getTimestampByDateString = (dateString) =>
        Math.round(new Date(dateString).getTime() / 1000);

    before(async function () {
        await this.dbCleaner.clearOrders();
        localCartItem = this.localCartItem;

        for (let index = 0; index < orderCount; index++) {
            const {orderId} = await createNewGuestOrder(this.magentoApi, localCartItem);
            await shipOrder(this.magentoApi, orderId);
        }
    });

    after(async function () {
        await this.dbCleaner.clearOrders();
    });

    it('should return orders and paging info according to parameters', async function () {
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

    it('should handle multiple store IDs', async function () {
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

    it('should filter for store IDs', async function () {
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

    it('should filter with sinceId', async function () {
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

    it('should filter with last_updated_from', async function () {
        const yesterday = new Date();
        yesterday.setDate(yesterday.getDate() - 1);
        const lastUpdatedFrom = yesterday.toISOString().split('T')[0];

        const apiPath = buildApiPath('/rest/V1/emarsys/orders', {
            page: 1,
            page_size: 2,
            store_id: '1,2',
            since_id: 0,
            last_updated_from: lastUpdatedFrom,
        });

        const response = await this.magentoApi.get({ path: apiPath });

        const lastUpdatedFromTime = getTimestampByDateString(lastUpdatedFrom);
        response.items.forEach((order) => {
            const orderUpdatedAtTime = getTimestampByDateString(order.updated_at);
            expect(orderUpdatedAtTime).to.be.greaterThan(lastUpdatedFromTime);
        });
    });

    it('should filter with last_updated_to', async function () {
        const yesterday = new Date();
        yesterday.setDate(yesterday.getDate() - 1);
        const lastUpdatedTo = yesterday.toISOString().split('T')[0];

        const apiPath = buildApiPath('/rest/V1/emarsys/orders', {
            page: 1,
            page_size: 2,
            store_id: '1,2',
            since_id: 0,
            last_updated_to: lastUpdatedTo,
        });

        const response = await this.magentoApi.get({ path: apiPath });

        const lastUpdatedToTime = getTimestampByDateString(lastUpdatedTo);
        response.items.forEach((order) => {
            const orderUpdatedAtTime = getTimestampByDateString(order.updated_at);
            expect(orderUpdatedAtTime).to.be.lessThan(lastUpdatedToTime);
        });
    });

    it('should filter with last_updated_from/to', async function () {
        const lastYear = new Date();
        lastYear.setFullYear(lastYear.getFullYear() - 1);
        const lastUpdatedFrom = lastYear.toISOString().split('T')[0];

        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        const lastUpdatedTo = tomorrow.toISOString().split('T')[0];

        const apiPath = buildApiPath('/rest/V1/emarsys/orders', {
            page: 1,
            page_size: 2,
            store_id: '1,2',
            since_id: 0,
            last_updated_from: lastUpdatedFrom,
            last_updated_to: lastUpdatedTo,
        });

        const response = await this.magentoApi.get({ path: apiPath });

        const lastUpdatedFromTime = getTimestampByDateString(lastUpdatedFrom);
        const lastUpdatedToTime = getTimestampByDateString(lastUpdatedTo);
        response.items.forEach((order) => {
            const orderUpdatedAtTime = getTimestampByDateString(order.updated_at);
            expect(orderUpdatedAtTime).to.be.greaterThan(lastUpdatedFromTime);
            expect(orderUpdatedAtTime).to.be.lessThan(lastUpdatedToTime);
        });
    });
});

'use strict';

describe('Refunds endpoint', function () {

    const buildApiPath = (basePath, params = {}) => {
        const query = new URLSearchParams(params).toString();
        return `${basePath}?${query}`;
    };

    const getTimestampByDateString = (dateString) =>
        Math.round(new Date(dateString).getTime() / 1000);

    it('should return refunds and paging info according to parameters', async function () {
        const apiPath = buildApiPath('/rest/V1/emarsys/refunds', {
            page: 1,
            page_size: 2,
            store_id: '1,2',
            since_id: 0
        });

        const response = await this.magentoApi.get({ path: apiPath });
        expect(response).to.have.property('items');
    });

    it('should filter with last_updated_from', async function () {
        const yesterday = new Date();
        yesterday.setDate(yesterday.getDate() - 1);
        const lastUpdatedFrom = yesterday.toISOString().split('T')[0];

        const apiPath = buildApiPath('/rest/V1/emarsys/refunds', {
            page: 1,
            page_size: 2,
            store_id: '1,2',
            since_id: 0,
            last_updated_from: lastUpdatedFrom,
        });

        const response = await this.magentoApi.get({ path: apiPath });

        const lastUpdatedFromTime = getTimestampByDateString(lastUpdatedFrom);
        response.items.forEach((refund) => {
            const refundUpdatedAtTime = getTimestampByDateString(refund.updated_at);
            expect(refundUpdatedAtTime).to.be.greaterThan(lastUpdatedFromTime);
        });
    });

    it('should filter with last_updated_to', async function () {
        const yesterday = new Date();
        yesterday.setDate(yesterday.getDate() - 1);
        const lastUpdatedTo = yesterday.toISOString().split('T')[0];

        const apiPath = buildApiPath('/rest/V1/emarsys/refunds', {
            page: 1,
            page_size: 2,
            store_id: '1,2',
            since_id: 0,
            last_updated_to: lastUpdatedTo,
        });

        const response = await this.magentoApi.get({ path: apiPath });

        const lastUpdatedToTime = getTimestampByDateString(lastUpdatedTo);
        response.items.forEach((refund) => {
            const refundUpdatedAtTime = getTimestampByDateString(refund.updated_at);
            expect(refundUpdatedAtTime).to.be.lessThan(lastUpdatedToTime);
        });
    });

    it('should filter with last_updated_from/to', async function () {
        const lastYear = new Date();
        lastYear.setFullYear(lastYear.getFullYear() - 1);
        const lastUpdatedFrom = lastYear.toISOString().split('T')[0];

        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        const lastUpdatedTo = tomorrow.toISOString().split('T')[0];

        const apiPath = buildApiPath('/rest/V1/emarsys/refunds', {
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
        response.items.forEach((refund) => {
            const refundUpdatedAtTime = getTimestampByDateString(refund.updated_at);
            expect(refundUpdatedAtTime).to.be.greaterThan(lastUpdatedFromTime);
            expect(refundUpdatedAtTime).to.be.lessThan(lastUpdatedToTime);
        });
    });
});

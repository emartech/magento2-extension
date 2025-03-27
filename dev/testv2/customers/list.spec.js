'use strict';

const customers = [
    {
        group_id: 0,
        dob: '1977-11-12',
        email: 'yolo@customer.net',
        firstname: 'Yolo',
        lastname: 'World',
        store_id: 1,
        website_id: 1,
        disable_auto_group_change: 0
    },
    {
        group_id: 0,
        dob: '1977-11-12',
        email: 'yolo2@customer.net',
        firstname: 'Yolo2',
        lastname: 'World',
        store_id: 1,
        website_id: 1,
        disable_auto_group_change: 0
    }
];

const getTimestampByDateString = (dateString) => {
    return Math.round(new Date(dateString).getTime() / 1000);
}

describe('Customers endpoint', function () {
    before(async function () {
        await this.magentoApi.execute('attributes', 'set', {
            websiteId: 1,
            type: 'customer',
            attributeCodes: ['emarsys_test_favorite_car']
        });
        for (const customer of customers) {
            await this.createCustomer(customer);
        }
    });

    after(async function () {
        await this.magentoApi.execute('attributes', 'set', {
            websiteId: 1,
            type: 'customer',
            attributeCodes: []
        });
        await this.db.raw(`DELETE
                           FROM ${this.getTableName('customer_entity')}
                           where email like "%@customer.net"`);
    });

    it('returns customers according to page and page_size inlcuding last_page', async function () {
        const page = 1;
        const limit = 2;

        const {customers, lastPage} = await this.magentoApi.execute('customers', 'getAll', {page, limit, websiteId: 1});
        const customer = customers[0];

        expect(customers.length).to.equal(2);
        expect(customer.id).to.equal(1);
        expect(lastPage).to.equal(2);

        expect(customer).to.have.property('id');
        expect(customer.email).to.be.a('string');
        expect(customer.firstname).to.be.a('string');
        expect(customer.lastname).to.be.a('string');
        expect(customer.billing_address).to.be.an('object');
        expect(customer.shipping_address).to.be.an('object');
        expect(customer).to.have.property('accepts_marketing');
        expect(customer).to.have.property('billing_address');
        expect(customer).to.have.property('shipping_address');
    });

    it('returns customers according to last_updated_from filter', async function () {
        const apiPath = `/rest/V1/emarsys/customers?page=1&page_size=2&website_id=1&only_reg=true`;

        let yesterday = new Date();
        yesterday.setDate(yesterday.getDate() - 1);
        let lastUpdatedFrom = yesterday.toISOString().split('T')[0];

        let response = await this.magentoApi.get({
            path: `${apiPath}&last_updated_from=${lastUpdatedFrom}`
        });

        let lastUpdatedFromTime = getTimestampByDateString(lastUpdatedFrom);
        for (let customer of response.customers) {
            let customerUpdatedAtTime = getTimestampByDateString(customer.updated_at);
            expect(customerUpdatedAtTime).to.be.greaterThan(lastUpdatedFromTime);
        }
    });

    it('returns customers according to last_updated_to filter', async function () {
        const apiPath = `/rest/V1/emarsys/customers?page=1&page_size=2&website_id=1&only_reg=true`;

        let yesterday = new Date();
        yesterday.setDate(yesterday.getDate() - 1);
        let lastUpdatedTo = yesterday.toISOString().split('T')[0];

        let response = await this.magentoApi.get({
            path: `${apiPath}&last_updated_to=${lastUpdatedTo}`
        });

        let lastUpdatedToTime = getTimestampByDateString(lastUpdatedTo);
        for (let customer of response.customers) {
            let customerUpdatedAtTime = getTimestampByDateString(customer.updated_at);
            expect(customerUpdatedAtTime).to.be.lessThan(lastUpdatedToTime);
        }
    });

    it('returns customers according to last_updated_from/to filter', async function () {
        const apiPath = `/rest/V1/emarsys/customers?page=1&page_size=2&website_id=1&only_reg=true`;

        let lastYear = new Date();
        lastYear.setFullYear(lastYear.getFullYear() - 1);
        let lastUpdatedFrom = lastYear.toISOString().split('T')[0];

        let tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        let lastUpdatedTo = tomorrow.toISOString().split('T')[0];

        let response = await this.magentoApi.get({
            path: `${apiPath}&last_updated_from=${lastUpdatedFrom}&last_updated_to=${lastUpdatedTo}`
        });

        let lastUpdatedFromTime = getTimestampByDateString(lastUpdatedFrom);
        let lastUpdatedToTime = getTimestampByDateString(lastUpdatedTo);

        for (let customer of response.customers) {
            let customerUpdatedAtTime = getTimestampByDateString(customer.updated_at);
            expect(customerUpdatedAtTime).to.be.greaterThan(lastUpdatedFromTime);
            expect(customerUpdatedAtTime).to.be.lessThan(lastUpdatedToTime);
        }
    });

    it('returns extra_fields for customers', async function () {
        const page = 1;
        const limit = 1;

        const {customers} = await this.magentoApi.execute('customers', 'getAll', {page, limit, websiteId: 1});
        const customer = customers[0];

        expect(customer.extra_fields[0].key).to.be.equal('emarsys_test_favorite_car');
        expect(customer.extra_fields[0].value).to.be.equal('ferrari');
    });
});

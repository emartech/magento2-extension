'use strict';

const createSubscriptionGetter = db => {
  return async email => {
    return await db
      .select()
      .from('newsletter_subscriber')
      .where({ subscriber_email: email })
      .first();
  };
};

const isSubscribed = subscription => {
  return subscription !== undefined && subscription.subscriber_status === 1;
};

const noCustomerEmail = 'no-customer@a.com';
const noCustomerEmail2 = 'still-no-customer@a.com';
const customerEmail = 'roni_cost@example.com';
const customerId = 1;
const websiteId = 1;
const storeId = 1;

describe('Subscriptions api', function() {
  let subscriptionFor;

  before(function() {
    subscriptionFor = createSubscriptionGetter(this.db);
  });

  describe('update', function() {
    afterEach(async function() {
      await this.db('newsletter_subscriber')
        .whereIn('subscriber_email', [noCustomerEmail, customerEmail])
        .delete();
    });

    describe('subscribe', function() {
      it('should set subscription without customer', async function() {
        expect(isSubscribed(await subscriptionFor(noCustomerEmail))).to.be.false;

        await this.magentoApi.execute('subscriptions', 'update', {
          subscriptions: [{ subscriber_email: noCustomerEmail, subscriber_status: true }]
        });

        expect(isSubscribed(await subscriptionFor(noCustomerEmail))).to.be.true;
      });

      it('should set subscription with customer', async function() {
        expect(isSubscribed(await subscriptionFor(customerEmail))).to.be.false;

        await this.magentoApi.execute('subscriptions', 'update', {
          subscriptions: [{ subscriber_email: customerEmail, subscriber_status: true, customer_id: customerId }]
        });

        expect(isSubscribed(await subscriptionFor(customerEmail))).to.be.true;
      });
    });

    describe('unsubscribe', function() {
      it('should unsubscribe without customer', async function() {
        await this.magentoApi.execute('subscriptions', 'update', {
          subscriptions: [{ subscriber_email: noCustomerEmail, subscriber_status: true }]
        });
        await this.magentoApi.execute('subscriptions', 'update', {
          subscriptions: [{ subscriber_email: noCustomerEmail, subscriber_status: false }]
        });

        expect(isSubscribed(await subscriptionFor(noCustomerEmail))).to.be.false;
      });

      it('should unsubscribe with customer', async function() {
        await this.magentoApi.execute('subscriptions', 'update', {
          subscriptions: [{ subscriber_email: customerEmail, subscriber_status: true, customer_id: customerId }]
        });
        await this.magentoApi.execute('subscriptions', 'update', {
          subscriptions: [{ subscriber_email: customerEmail, subscriber_status: false, customer_id: customerId }]
        });

        expect(isSubscribed(await subscriptionFor(customerEmail))).to.be.false;
      });
    });
  });
  describe('get', function() {
    let customerEmail2;
    let customerId2;

    before(async function() {
      customerEmail2 = this.customer.email;
      customerId2 = this.customer.entityId;

      await this.db
        .insert([
          { subscriber_email: noCustomerEmail, subscriber_status: 1, store_id: storeId },
          { subscriber_email: noCustomerEmail2, subscriber_status: 3, store_id: storeId },
          { subscriber_email: customerEmail, subscriber_status: 1, customer_id: customerId, store_id: storeId },
          {
            subscriber_email: customerEmail2,
            subscriber_status: 3,
            customer_id: customerId2,
            store_id: storeId
          }
        ])
        .into('newsletter_subscriber');
    });

    it('should list all subscriber without filters', async function() {
      const expectedSubscriptions = {
        subscriptions: [
          {
            customer_id: 0,
            store_id: storeId,
            subscriber_email: noCustomerEmail,
            subscriber_status: '1',
            website_id: websiteId
          },
          {
            customer_id: 0,
            store_id: storeId,
            subscriber_email: noCustomerEmail2,
            subscriber_status: '3',
            website_id: websiteId
          },
          {
            customer_id: customerId,
            store_id: storeId,
            subscriber_email: customerEmail,
            subscriber_status: '1',
            website_id: websiteId
          },
          {
            customer_id: customerId2,
            store_id: storeId,
            subscriber_email: customerEmail2,
            subscriber_status: '3',
            website_id: websiteId
          }
        ],
        total_count: 4
      };

      const actualSubscriptions = await this.magentoApi.execute('subscriptions', 'list', { websiteId });

      expect(actualSubscriptions.total_count).to.be.eql(expectedSubscriptions.total_count);
      expect(actualSubscriptions.subscriptions).to.containSubset(expectedSubscriptions.subscriptions);
    });

    it('should filter with subscribed status true', async function() {
      const expectedSubscriptions = {
        subscriptions: [
          {
            customer_id: 0,
            store_id: storeId,
            subscriber_email: noCustomerEmail,
            subscriber_status: '1',
            website_id: websiteId
          },
          {
            customer_id: customerId,
            store_id: storeId,
            subscriber_email: customerEmail,
            subscriber_status: '1',
            website_id: websiteId
          }
        ],
        total_count: 2,
        current_page: 1,
        last_page: 1,
        page_size: 1000
      };

      const actualSubscriptions = await this.magentoApi.execute('subscriptions', 'list', {
        subscribed: true,
        websiteId
      });

      expect(actualSubscriptions).to.be.eql(expectedSubscriptions);
    });

    it('should filter with subscribed false', async function() {
      const expectedSubscriptions = {
        subscriptions: [
          {
            customer_id: 0,
            store_id: storeId,
            subscriber_email: noCustomerEmail2,
            subscriber_status: '3',
            website_id: websiteId
          },
          {
            customer_id: customerId2,
            store_id: storeId,
            subscriber_email: customerEmail2,
            subscriber_status: '3',
            website_id: websiteId
          }
        ],
        total_count: 2,
        current_page: 1,
        last_page: 1,
        page_size: 1000
      };

      const actualSubscriptions = await this.magentoApi.execute('subscriptions', 'list', {
        subscribed: false,
        onlyGuest: false,
        websiteId
      });

      expect(actualSubscriptions).to.be.eql(expectedSubscriptions);
    });

    it('should filter for not customers', async function() {
      const expectedSubscriptions = {
        subscriptions: [
          {
            customer_id: 0,
            store_id: storeId,
            subscriber_email: noCustomerEmail,
            subscriber_status: '1'
          },
          {
            customer_id: 0,
            store_id: storeId,
            subscriber_email: noCustomerEmail2,
            subscriber_status: '3'
          }
        ],
        total_count: 2,
        current_page: 1,
        last_page: 1,
        page_size: 1000
      };

      const actualSubscriptions = await this.magentoApi.execute('subscriptions', 'list', {
        onlyGuest: true,
        websiteId
      });

      expect(actualSubscriptions.total_count).to.be.eql(expectedSubscriptions.total_count);
      expect(actualSubscriptions.subscriptions).to.containSubset(expectedSubscriptions.subscriptions);
    });
  });
});

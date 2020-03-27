'use strict';

const createSubscriptionGetter = (db, tablePrefix) => {
  return async email => {
    return await db
      .select()
      .from(`${tablePrefix}newsletter_subscriber`)
      .where({ subscriber_email: email })
      .first();
  };
};

const isSubscribed = subscription => {
  return subscription !== undefined && parseInt(subscription.subscriber_status) === 1;
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
    subscriptionFor = createSubscriptionGetter(this.db, this.getTableName(''));
  });

  describe('update', function() {
    afterEach(async function() {
      await this.db(this.getTableName('newsletter_subscriber'))
        .whereIn('subscriber_email', [noCustomerEmail, noCustomerEmail2, customerEmail])
        .delete();
    });

    it('should handle multiple subscriptions at once and create only for customers', async function() {
      try {
        await this.magentoApi.execute('subscriptions', 'update', {
          subscriptions: [
            { subscriber_email: noCustomerEmail, subscriber_status: true, website_id: 1, customer_id: 0 },
            { subscriber_email: customerEmail, subscriber_status: true, website_id: 1, customer_id: customerId }
          ]
        });
      } catch (error) {
        const message = error.response.data.message || 'POST subscriptions/update failed';
        expect.fail(error.response.status, 200, message);
      }

      const subscribers = await this.magentoApi.execute('subscriptions', 'list', {
        page: 1,
        limit: 100,
        onlyGuest: false,
        websiteId: 1
      });
      const [customer] = subscribers.subscriptions;

      expect(isSubscribed(customer)).to.be.true;
      expect(customer.website_id).to.equal(1);
      expect(await subscriptionFor(noCustomerEmail)).to.be.undefined;
    });

    it('should handle multiple subscription updates at once and update only for existing', async function() {
      await this.db(this.getTableName('newsletter_subscriber')).insert([
        { subscriber_email: customerEmail, subscriber_status: 3, store_id: 1, customer_id: customerId },
        { subscriber_email: noCustomerEmail, subscriber_status: 3, store_id: 1, customer_id: 0 },
        { subscriber_email: noCustomerEmail2, subscriber_status: 3, store_id: 1, customer_id: 0 }
      ]);

      try {
        await this.magentoApi.execute('subscriptions', 'update', {
          subscriptions: [
            { subscriber_email: customerEmail, subscriber_status: true, website_id: 1, customer_id: customerId },
            { subscriber_email: noCustomerEmail, subscriber_status: true, website_id: 1, customer_id: 0 },
            { subscriber_email: noCustomerEmail2, subscriber_status: true, website_id: 1, customer_id: 0 }
          ]
        });
      } catch (error) {
        const message = error.response.data.message || 'POST subscriptions/update failed';
        expect.fail(error.response.status, 200, message);
      }

      const subscribers = await this.magentoApi.execute('subscriptions', 'list', {
        page: 1,
        limit: 100,
        onlyGuest: false,
        websiteId: 1
      });
      const [customer, noCustomer, noCustomer2] = subscribers.subscriptions;

      expect(isSubscribed(customer)).to.be.true;
      expect(customer.website_id).to.equal(1);
      expect(isSubscribed(noCustomer)).to.be.true;
      expect(noCustomer.website_id).to.equal(1);
      expect(isSubscribed(noCustomer2)).to.be.true;
      expect(noCustomer2.website_id).to.equal(1);
    });

    context('subscribe', function() {
      it('should set subscription without customer', async function() {
        await this.db(this.getTableName('newsletter_subscriber')).insert({
          subscriber_email: noCustomerEmail,
          subscriber_status: 3,
          store_id: 1,
          customer_id: 0
        });

        expect(isSubscribed(await subscriptionFor(noCustomerEmail))).to.be.false;

        await this.magentoApi.execute('subscriptions', 'update', {
          subscriptions: [{ subscriber_email: noCustomerEmail, subscriber_status: true, website_id: 1, customer_id: 0 }]
        });

        expect(isSubscribed(await subscriptionFor(noCustomerEmail))).to.be.true;
      });

      it('should set subscription with customer', async function() {
        expect(isSubscribed(await subscriptionFor(customerEmail))).to.be.false;

        await this.magentoApi.execute('subscriptions', 'update', {
          subscriptions: [
            { subscriber_email: customerEmail, subscriber_status: true, website_id: 1, customer_id: customerId }
          ]
        });

        expect(isSubscribed(await subscriptionFor(customerEmail))).to.be.true;
      });
    });

    context('unsubscribe', function() {
      it('should unsubscribe without customer', async function() {
        await this.db(this.getTableName('newsletter_subscriber')).insert({
          subscriber_email: noCustomerEmail,
          subscriber_status: 1,
          store_id: 1,
          customer_id: 0
        });

        await this.magentoApi.execute('subscriptions', 'update', {
          subscriptions: [
            { subscriber_email: noCustomerEmail, subscriber_status: false, website_id: 1, customer_id: 0 }
          ]
        });

        const subscriber = await subscriptionFor(noCustomerEmail);
        expect(subscriber).not.to.be.undefined;
        expect(isSubscribed(subscriber)).to.be.false;
      });

      it('should unsubscribe with customer', async function() {
        await this.magentoApi.execute('subscriptions', 'update', {
          subscriptions: [
            { subscriber_email: customerEmail, subscriber_status: true, website_id: 1, customer_id: customerId }
          ]
        });
        await this.magentoApi.execute('subscriptions', 'update', {
          subscriptions: [
            { subscriber_email: customerEmail, subscriber_status: false, website_id: 1, customer_id: customerId }
          ]
        });

        expect(isSubscribed(await subscriptionFor(customerEmail))).to.be.false;
      });
    });

    it('should catch and return exceptions and continue with the update', async function() {
      const nonExistingCustomerId = 999;
      const expectedErrors = [
        {
          email: noCustomerEmail,
          customer_id: nonExistingCustomerId,
          message: `No such entity with customerId = ${nonExistingCustomerId}`
        }
      ];

      expect(isSubscribed(await subscriptionFor(customerEmail))).to.be.false;
      const updateResponse = await this.magentoApi.execute('subscriptions', 'update', {
        subscriptions: [
          {
            subscriber_email: noCustomerEmail,
            subscriber_status: true,
            website_id: 1,
            customer_id: nonExistingCustomerId
          },
          { subscriber_email: customerEmail, subscriber_status: true, website_id: 1, customer_id: customerId }
        ]
      });

      const subscribers = await this.magentoApi.execute('subscriptions', 'list', {
        page: 1,
        limit: 100,
        onlyGuest: false,
        websiteId: 1
      });

      const filteredSubscriptions = subscribers.subscriptions.filter(
        subscription => subscription.subscriber_email === noCustomerEmail
      );
      expect(filteredSubscriptions).to.be.empty;
      expect(isSubscribed(await subscriptionFor(customerEmail))).to.be.true;

      expect(updateResponse.errors).to.be.eql(expectedErrors);
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
        .into(this.getTableName('newsletter_subscriber'));
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

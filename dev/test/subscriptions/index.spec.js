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

describe.skip('Subscriptions api', function() {
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

        await this.magentoApi.updateSubscriptions({
          subscriptions: [{ subscriber_email: noCustomerEmail, subscriber_status: true }]
        });

        expect(isSubscribed(await subscriptionFor(noCustomerEmail))).to.be.true;
      });

      it('should set subscription with customer', async function() {
        expect(isSubscribed(await subscriptionFor(customerEmail))).to.be.false;

        await this.magentoApi.updateSubscriptions({
          subscriptions: [{ subscriber_email: customerEmail, subscriber_status: true, customer_id: customerId }]
        });

        expect(isSubscribed(await subscriptionFor(customerEmail))).to.be.true;
      });
    });

    describe('unsubscribe', function() {
      it('should unsubscribe without customer', async function() {
        await this.magentoApi.updateSubscriptions({
          subscriptions: [{ subscriber_email: noCustomerEmail, subscriber_status: true }]
        });
        await this.magentoApi.updateSubscriptions({
          subscriptions: [{ subscriber_email: noCustomerEmail, subscriber_status: false }]
        });

        expect(isSubscribed(await subscriptionFor(noCustomerEmail))).to.be.false;
      });

      it('should unsubscribe with customer', async function() {
        await this.magentoApi.updateSubscriptions({
          subscriptions: [{ subscriber_email: customerEmail, subscriber_status: true, customer_id: customerId }]
        });
        await this.magentoApi.updateSubscriptions({
          subscriptions: [{ subscriber_email: customerEmail, subscriber_status: false, customer_id: customerId }]
        });

        expect(isSubscribed(await subscriptionFor(customerEmail))).to.be.false;
      });
    });
  });
  describe('list', function() {
    let customerEmail2;
    let customerId2;

    before(async function() {
      customerEmail2 = this.customer.email;
      customerId2 = this.customer.entityId;

      await this.magentoApi.updateSubscriptions({
        subscriptions: [
          { subscriber_email: noCustomerEmail2, subscriber_status: true },
          { subscriber_email: customerEmail2, subscriber_status: true, customer_id: customerId2 }
        ]
      });

      await this.magentoApi.updateSubscriptions({
        subscriptions: [
          { subscriber_email: noCustomerEmail, subscriber_status: true },
          { subscriber_email: noCustomerEmail2, subscriber_status: false },
          { subscriber_email: customerEmail, subscriber_status: true, customer_id: customerId },
          { subscriber_email: customerEmail2, subscriber_status: false, customer_id: customerId2 }
        ]
      });
    });

    it('should list all subscriber without filters', async function() {
      const expectedSubscriptions = {
        subscriptions: [
          {
            customer_id: '0',
            store_id: '0',
            subscriber_email: noCustomerEmail,
            subscriber_status: '1'
          },
          {
            customer_id: '' + customerId,
            store_id: '0',
            subscriber_email: customerEmail,
            subscriber_status: '1'
          },
          {
            customer_id: '0',
            store_id: '0',
            subscriber_email: noCustomerEmail2,
            subscriber_status: '3'
          },
          {
            customer_id: '' + customerId2,
            store_id: '0',
            subscriber_email: customerEmail2,
            subscriber_status: '3'
          }
        ],
        subscriptionCount: 4
      };

      const actualSubscriptions = await this.magentoApi.getSubscriptions({ withCustomers: true });

      expect(actualSubscriptions.subscriptionCount).to.be.eql(expectedSubscriptions.subscriptionCount);
      expect(actualSubscriptions.subscriptions).to.containSubset(expectedSubscriptions.subscriptions);
    });

    it('should give specific subscriber for email filter', async function() {
      const expectedSubscriptions = {
        subscriptions: [
          {
            customer_id: '1',
            store_id: '0',
            subscriber_email: 'roni_cost@example.com',
            subscriber_status: '1'
          }
        ],
        subscriptionCount: 1
      };

      const actualSubscriptions = await this.magentoApi.getSubscriptions({
        emails: [customerEmail],
        withCustomers: true
      });

      expect(actualSubscriptions).to.be.eql(expectedSubscriptions);
    });

    it('should give empty result for not existing email filter', async function() {
      const expectedSubscriptions = {
        subscriptions: [],
        subscriptionCount: 0
      };

      const actualSubscriptions = await this.magentoApi.getSubscriptions({
        emails: ['not-a-known-address@a.com'],
        withCustomers: true
      });

      expect(actualSubscriptions).to.be.eql(expectedSubscriptions);
    });

    it('should filter with subscribed true', async function() {
      const expectedSubscriptions = {
        subscriptions: [
          {
            customer_id: '0',
            store_id: '0',
            subscriber_email: noCustomerEmail,
            subscriber_status: '1'
          },
          {
            customer_id: '' + customerId,
            store_id: '0',
            subscriber_email: customerEmail,
            subscriber_status: '1'
          }
        ],
        subscriptionCount: 2
      };

      const actualSubscriptions = await this.magentoApi.getSubscriptions({ subscribed: true, withCustomers: true });

      expect(actualSubscriptions).to.be.eql(expectedSubscriptions);
    });

    it('should filter with subscribed false', async function() {
      const expectedSubscriptions = {
        subscriptions: [
          {
            customer_id: '0',
            store_id: '0',
            subscriber_email: noCustomerEmail2,
            subscriber_status: '3'
          },
          {
            customer_id: '' + customerId2,
            store_id: '0',
            subscriber_email: customerEmail2,
            subscriber_status: '3'
          }
        ],
        subscriptionCount: 2
      };

      const actualSubscriptions = await this.magentoApi.getSubscriptions({ subscribed: false, withCustomers: true });

      expect(actualSubscriptions).to.be.eql(expectedSubscriptions);
    });

    it('should filter for not customers', async function() {
      const expectedSubscriptions = {
        subscriptions: [
          {
            customer_id: '0',
            store_id: '0',
            subscriber_email: noCustomerEmail,
            subscriber_status: '1'
          },
          {
            customer_id: '0',
            store_id: '0',
            subscriber_email: noCustomerEmail2,
            subscriber_status: '3'
          }
        ],
        subscriptionCount: 2
      };

      const actualSubscriptions = await this.magentoApi.getSubscriptions({ withCustomers: false });

      expect(actualSubscriptions.subscriptionCount).to.be.eql(expectedSubscriptions.subscriptionCount);
      expect(actualSubscriptions.subscriptions).to.containSubset(expectedSubscriptions.subscriptions);
    });
  });
});

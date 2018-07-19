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
const customerEmail = 'roni_cost@example.com';
const customerId = 1;

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
    before(async function() {
      await this.magentoApi.updateSubscriptions({
        subscriptions: [
          { subscriber_email: noCustomerEmail, subscriber_status: true },
          { subscriber_email: customerEmail, subscriber_status: true, customer_id: customerId }
        ]
      });
    });

    it('should list all subscriber when no emails given', async function() {
      const expectedSubscriptions = {
        subscriptions: [
          {
            customer_id: '0',
            subscriber_email: 'no-customer@a.com',
            subscriber_status: '1'
          },
          {
            customer_id: '1',
            subscriber_email: 'roni_cost@example.com',
            subscriber_status: '1'
          }
        ],
        total_count: 2
      };

      const actualSubscriptions = await this.magentoApi.getSubscriptions({});

      expect(actualSubscriptions).to.be.eql(expectedSubscriptions);
    });

    it('should give specific subscriber for email', async function() {
      const expectedSubscriptions = {
        subscriptions: [
          {
            customer_id: '1',
            subscriber_email: 'roni_cost@example.com',
            subscriber_status: '1'
          }
        ],
        total_count: 1
      };

      const actualSubscriptions = await this.magentoApi.getSubscriptions({ emails: [customerEmail] });

      expect(actualSubscriptions).to.be.eql(expectedSubscriptions);
    });

    it('should give empty result for not existing email', async function() {
      const expectedSubscriptions = {
        subscriptions: [],
        total_count: 0
      };

      const actualSubscriptions = await this.magentoApi.getSubscriptions({ emails: ['not-a-known-address@a.com'] });

      expect(actualSubscriptions).to.be.eql(expectedSubscriptions);
    });
  });
});

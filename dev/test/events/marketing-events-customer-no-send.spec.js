'use strict';

const mailhog = require('../helpers/mailhog');

const customer = {
  group_id: 0,
  dob: '1977-11-12',
  email: 'yolo@yolo.net',
  firstname: 'Yolo',
  lastname: 'World',
  store_id: 1,
  website_id: 1,
  gender: 1,
  disable_auto_group_change: 0,
  custom_attributes: [
    {
      attribute_code: 'emarsys_test_favorite_car',
      value: 'skoda'
    }
  ],
  addresses: [
    {
      region_id: 32,
      country_id: 'US',
      street: ['123 Main Street', 'PO Box 321'],
      firstname: 'John',
      lastname: 'Doe',
      company: 'ABC Manufacturing',
      telephone: '555-555-5555',
      city: 'Boston',
      postcode: '02115',
      default_shipping: true,
      default_billing: true
    }
  ]
};

const newsletterCustomer = {
  group_id: 0,
  dob: '1977-11-12',
  email: 'yolo@newsletter.net',
  firstname: 'Yolo',
  lastname: 'Newsletter',
  store_id: 1,
  website_id: 1,
  disable_auto_group_change: 0
};

describe('Marketing events: customer', function () {
  beforeEach(async function () {
    await mailhog.clearMails();
  });

  afterEach(async function () {
    await this.db.truncate(this.getTableName('password_reset_request_event'));
    await this.db.raw(`DELETE FROM ${this.getTableName('customer_entity')} where email = "yolo@yolo.net"`);
  });

  after(async function () {
    await this.db.raw(`DELETE FROM ${this.getTableName('newsletter_subscriber')}`);
    await this.db.raw(
      `DELETE FROM ${this.getTableName(
        'customer_entity'
      )} where email = "yolo@yolo.net" OR email = "yolo@newsletter.net"`
    );
    await mailhog.clearMails();
  });

  context('if collectMarketingEvents turned on', function () {
    before(async function () {
      await this.magentoApi.execute('attributes', 'set', {
        websiteId: 1,
        type: 'customer',
        attributeCodes: ['emarsys_test_favorite_car', 'gender']
      });

      await this.magentoApi.execute('attributes', 'set', {
        websiteId: 1,
        type: 'customer_address',
        attributeCodes: ['region_id']
      });
    });

    after(async function () {
      await this.magentoApi.execute('attributes', 'set', {
        websiteId: 1,
        type: 'customer',
        attributeCodes: []
      });

      await this.magentoApi.execute('attributes', 'set', {
        websiteId: 1,
        type: 'customer_address',
        attributeCodes: []
      });
    });

    context('magentoSendEmails config is disabled', function () {
      before(async function () {
        await this.magentoApi.execute('config', 'set', {
          websiteId: 1,
          config: { collectMarketingEvents: 'enabled', magentoSendEmail: 'disabled' }
        });
      });

      it('should create customer_new_account_registered_no_password event', async function () {
        await this.createCustomer(customer);

        const events = await this.db.select().from(this.getTableName('emarsys_events_data'));

        expect(events.length).to.be.equal(1);

        const event = events[0];
        expect(event.event_type).to.be.equal('customer_new_account_registered_no_password');

        const eventData = JSON.parse(event.event_data);
        expect(eventData.customer.email).to.eql(customer.email);
        expect(eventData.customer.extra_fields).to.eql([
          { key: 'emarsys_test_favorite_car', value: 'skoda', text_value: null },
          { key: 'gender', value: '1', text_value: 'Male' }
        ]);
        expect(eventData.customer.billing_address.extra_fields).to.eql([
          { key: 'region_id', value: '32', text_value: 'Massachusetts' }
        ]);
        expect(eventData.customer.shipping_address.extra_fields).to.eql([
          { key: 'region_id', value: '32', text_value: 'Massachusetts' }
        ]);
        expect(event.website_id).to.equal(1);
        expect(event.store_id).to.equal(1);

        const emailsSentTo = await mailhog.getSentAddresses();

        expect(emailsSentTo).to.eql([]);
      });

      it('should create customer_new_account_registered event', async function () {
        await this.createCustomer(customer, 'Password1234');

        const events = await this.db.select().from(this.getTableName('emarsys_events_data'));

        expect(events.length).to.be.equal(1);

        const event = events[0];
        expect(event.event_type).to.be.equal('customer_new_account_registered');

        const eventData = JSON.parse(event.event_data);
        expect(eventData.customer.email).to.eql(customer.email);
        expect(eventData.customer.extra_fields).to.eql([
          { key: 'emarsys_test_favorite_car', value: 'skoda', text_value: null },
          { key: 'gender', value: '1', text_value: 'Male' }
        ]);
        expect(eventData.customer.billing_address.extra_fields).to.eql([
          { key: 'region_id', value: '32', text_value: 'Massachusetts' }
        ]);
        expect(eventData.customer.shipping_address.extra_fields).to.eql([
          { key: 'region_id', value: '32', text_value: 'Massachusetts' }
        ]);
        expect(event.website_id).to.equal(1);
        expect(event.store_id).to.equal(1);

        const emailsSentTo = await mailhog.getSentAddresses();

        expect(emailsSentTo).to.eql([]);
      });

      context('with email confirmation needed', function () {
        before(async function () {
          await this.db
            .insert({ scope: 'default', scope_id: 0, path: 'customer/create_account/confirm', value: 1 })
            .into(this.getTableName('core_config_data'));

          // this is for invalidating config cache
          await this.magentoApi.execute('config', 'set', {
            websiteId: 0,
            config: {
              merchantId: `itsaflush${new Date().getTime()}`
            }
          });
        });

        after(async function () {
          await this.db.raw(
            `DELETE FROM ${this.getTableName('core_config_data')} WHERE path="customer/create_account/confirm"`
          );
        });

        it('should create customer_new_account_confirmation event', async function () {
          await this.createCustomer(customer, 'Password1234');

          const events = await this.db.select().from(this.getTableName('emarsys_events_data'));

          expect(events.length).to.be.equal(1);

          const event = events[0];
          expect(event.event_type).to.be.equal('customer_new_account_confirmation');

          const eventData = JSON.parse(event.event_data);
          expect(eventData.customer.email).to.eql(customer.email);
          expect(eventData.customer.extra_fields).to.eql([
            { key: 'emarsys_test_favorite_car', value: 'skoda', text_value: null },
            { key: 'gender', value: '1', text_value: 'Male' }
          ]);
          expect(eventData.customer.billing_address.extra_fields).to.eql([
            { key: 'region_id', value: '32', text_value: 'Massachusetts' }
          ]);
          expect(eventData.customer.shipping_address.extra_fields).to.eql([
            { key: 'region_id', value: '32', text_value: 'Massachusetts' }
          ]);
          expect(event.website_id).to.equal(1);
          expect(event.store_id).to.equal(1);

          const emailsSentTo = await mailhog.getSentAddresses();

          expect(emailsSentTo).to.eql([]);
        });
      });

      it('should create customer_password_reset_confirmation event', async function () {
        await this.magentoApi.put({
          path: '/index.php/rest/V1/customers/password',
          payload: {
            email: this.customer.email,
            template: 'email_reset',
            websiteId: this.customer.website_id
          }
        });

        const events = await this.db.select().from(this.getTableName('emarsys_events_data'));

        expect(events.length).to.be.equal(1);

        const event = events[0];

        expect(event.event_type).to.be.equal('customer_password_reset_confirmation');

        const eventData = JSON.parse(event.event_data);
        expect(eventData.customer.email).to.equal(this.customer.email);
        expect(eventData.customer.rp_token).not.to.be.undefined;
        expect(eventData.customer.rp_token_created_at).not.to.be.undefined;
        expect(event.website_id).to.equal(1);
        expect(event.store_id).to.equal(1);

        const emailsSentTo = await mailhog.getSentAddresses();

        expect(emailsSentTo).to.eql([]);
      });

      it('should create customer_password_reminder event', async function () {
        await this.magentoApi.put({
          path: '/index.php/rest/V1/customers/password',
          payload: {
            email: this.customer.email,
            template: 'email_reminder',
            websiteId: this.customer.website_id
          }
        });

        const events = await this.db.select().from(this.getTableName('emarsys_events_data'));

        expect(events.length).to.be.equal(1);

        const event = events[0];

        expect(event.event_type).to.be.equal('customer_password_reminder');

        const eventData = JSON.parse(event.event_data);
        expect(eventData.customer.email).to.equal(this.customer.email);
        expect(eventData.customer.rp_token).not.to.be.undefined;
        expect(eventData.customer.rp_token_created_at).not.to.be.undefined;
        expect(event.website_id).to.equal(1);
        expect(event.store_id).to.equal(1);

        const emailsSentTo = await mailhog.getSentAddresses();

        expect(emailsSentTo).to.eql([]);
      });

      context('and if newsletter/subscription/confirm', function () {
        let subscriber;

        before(async function () {
          subscriber = await this.createCustomer(newsletterCustomer, 'abcD1234');
        });

        beforeEach(async function () {
          await this.db.raw(`DELETE FROM ${this.getTableName('newsletter_subscriber')}`);
          await this.db.raw(`DELETE FROM ${this.getTableName('emarsys_events_data')}`);
        });

        after(async function () {
          await this.db.raw(
            `DELETE FROM ${this.getTableName('core_config_data')} WHERE path="newsletter/subscription/confirm"`
          );
          await this.db.raw(`DELETE FROM ${this.getTableName('newsletter_subscriber')}`);
          await this.db.raw(`DELETE FROM ${this.getTableName('customer_entity')} WHERE email="yolo@newsletter.net"`);
        });

        context('is disabled', function () {
          before(async function () {
            await this.db.raw(
              `DELETE FROM ${this.getTableName('core_config_data')} WHERE path="newsletter/subscription/confirm"`
            );
            // this is for invalidating config cache
            await this.magentoApi.execute('config', 'set', {
              websiteId: 1,
              config: {
                merchantId: `itsaflush${new Date().getTime()}`
              }
            });
          });

          it('should create newsletter_send_confirmation_success_email event', async function () {
            await this.magentoApi.put({
              path: `/index.php/rest/V1/customers/${subscriber.entityId}`,
              payload: {
                customer: {
                  id: subscriber.entityId,
                  email: subscriber.email,
                  firstname: subscriber.firstname,
                  lastname: subscriber.lastname,
                  store_id: 1,
                  website_id: 1,
                  extension_attributes: {
                    is_subscribed: true
                  }
                }
              }
            });

            const event = await this.db.select().from(this.getTableName('emarsys_events_data')).first();
            const createdEventData = JSON.parse(event.event_data);

            expect(event.event_type).to.equal('newsletter_send_confirmation_success_email');
            expect(event.website_id).to.equal(1);
            expect(event.store_id).to.equal(1);
            expect(createdEventData.subscriber.subscriber_email).to.equal(subscriber.email);
            expect(createdEventData.subscriber.subscriber_status).to.equal(1);
            expect(createdEventData.subscriber.subscriber_email).to.equal(subscriber.email);
            expect(createdEventData.subscriber.subscriber_status).to.equal(1);
            expect(createdEventData.customer.firstname).to.equal(subscriber.firstname);
            expect(createdEventData.customer.lastname).to.equal(subscriber.lastname);

            const emailsSentTo = await mailhog.getSentAddresses();

            expect(emailsSentTo).to.eql([]);
          });

          // bug: sends newsletter_send_confirmation_request_email on unsubscribe
          it('should create newsletter_send_unsubscription_email event', async function () {
            await this.magentoApi.put({
              path: `/index.php/rest/V1/customers/${subscriber.entityId}`,
              payload: {
                customer: {
                  id: subscriber.entityId,
                  email: subscriber.email,
                  firstname: subscriber.firstname,
                  lastname: subscriber.lastname,
                  store_id: 1,
                  website_id: 1,
                  extension_attributes: {
                    is_subscribed: true
                  }
                }
              }
            });

            await this.db.raw(`DELETE FROM ${this.getTableName('emarsys_events_data')}`);

            await this.magentoApi.put({
              path: `/index.php/rest/V1/customers/${subscriber.entityId}`,
              payload: {
                customer: {
                  id: subscriber.entityId,
                  email: subscriber.email,
                  firstname: subscriber.firstname,
                  lastname: subscriber.lastname,
                  store_id: 1,
                  website_id: 1,
                  extension_attributes: {
                    is_subscribed: false
                  }
                }
              }
            });

            const event = await this.db.select().from(this.getTableName('emarsys_events_data')).first();
            const createdEventData = JSON.parse(event.event_data);

            expect(event.event_type).to.equal('newsletter_send_unsubscription_email');
            expect(event.website_id).to.equal(1);
            expect(event.store_id).to.equal(1);
            expect(createdEventData.subscriber.subscriber_email).to.equal(subscriber.email);
            expect(createdEventData.subscriber.subscriber_status).to.equal(3);
            expect(createdEventData.subscriber.subscriber_email).to.equal(subscriber.email);
            expect(createdEventData.subscriber.subscriber_status).to.equal(3);
            expect(createdEventData.customer.firstname).to.equal(subscriber.firstname);
            expect(createdEventData.customer.lastname).to.equal(subscriber.lastname);

            const emailsSentTo = await mailhog.getSentAddresses();

            expect(emailsSentTo).to.eql([]);
          });
        });

        context('is enabled', function () {
          before(async function () {
            await this.db
              .insert({ scope: 'default', scope_id: 0, path: 'newsletter/subscription/confirm', value: 1 })
              .into(this.getTableName('core_config_data'));

            // this is for invalidating config cache
            await this.magentoApi.execute('config', 'set', {
              websiteId: 0,
              config: {
                merchantId: `itsaflush${new Date().getTime()}`
              }
            });
          });

          after(async function () {
            await this.db.raw(
              `DELETE FROM ${this.getTableName('core_config_data')} WHERE path="newsletter/subscription/confirm"`
            );
          });

          it('should create newsletter_send_confirmation_request_email event', async function () {
            await this.magentoApi.put({
              path: `/index.php/rest/V1/customers/${subscriber.entityId}`,
              payload: {
                customer: {
                  id: subscriber.entityId,
                  email: subscriber.email,
                  firstname: subscriber.firstname,
                  lastname: subscriber.lastname,
                  store_id: 1,
                  website_id: 1,
                  extension_attributes: {
                    is_subscribed: true
                  }
                }
              }
            });

            const event = await this.db.select().from(this.getTableName('emarsys_events_data')).first();
            const createdEventData = JSON.parse(event.event_data);

            expect(event.event_type).to.equal('newsletter_send_confirmation_request_email');
            expect(createdEventData.subscriber.subscriber_status).to.equal(2);

            expect(event.website_id).to.equal(1);
            expect(event.store_id).to.equal(1);
            expect(createdEventData.subscriber.subscriber_email).to.equal(subscriber.email);
            expect(createdEventData.customer.firstname).to.equal(subscriber.firstname);
            expect(createdEventData.customer.lastname).to.equal(subscriber.lastname);

            const emailsSentTo = await mailhog.getSentAddresses();

            expect(emailsSentTo).to.eql([]);
          });

          it('should create newsletter_send_unsubscription_email event', async function () {
            await this.magentoApi.put({
              path: `/index.php/rest/V1/customers/${subscriber.entityId}`,
              payload: {
                customer: {
                  id: subscriber.entityId,
                  email: subscriber.email,
                  firstname: subscriber.firstname,
                  lastname: subscriber.lastname,
                  store_id: 1,
                  website_id: 1,
                  extension_attributes: {
                    is_subscribed: true
                  }
                }
              }
            });

            await this.db(this.getTableName('newsletter_subscriber')).update('subscriber_status', 1);

            await this.db.raw(`DELETE FROM ${this.getTableName('emarsys_events_data')}`);

            await this.magentoApi.put({
              path: `/index.php/rest/V1/customers/${subscriber.entityId}`,
              payload: {
                customer: {
                  id: subscriber.entityId,
                  email: subscriber.email,
                  firstname: subscriber.firstname,
                  lastname: subscriber.lastname,
                  store_id: 1,
                  website_id: 1,
                  extension_attributes: {
                    is_subscribed: false
                  }
                }
              }
            });

            const event = await this.db.select().from(this.getTableName('emarsys_events_data')).first();
            const createdEventData = JSON.parse(event.event_data);

            expect(event.event_type).to.equal('newsletter_send_unsubscription_email');
            expect(event.website_id).to.equal(1);
            expect(event.store_id).to.equal(1);
            expect(createdEventData.subscriber.subscriber_email).to.equal(subscriber.email);
            expect(createdEventData.subscriber.subscriber_status).to.equal(3);
            expect(createdEventData.subscriber.subscriber_email).to.equal(subscriber.email);
            expect(createdEventData.subscriber.subscriber_status).to.equal(3);
            expect(createdEventData.customer.firstname).to.equal(subscriber.firstname);
            expect(createdEventData.customer.lastname).to.equal(subscriber.lastname);

            const emailsSentTo = await mailhog.getSentAddresses();

            expect(emailsSentTo).to.eql([]);
          });
        });
      });
    });
  });
});

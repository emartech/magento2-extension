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
  disable_auto_group_change: 0,
  custom_attributes: [
    {
      attribute_code: 'emarsys_test_favorite_car',
      value: 'skoda'
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

  context('if collectMarketingEvents turned off', function () {
    before(async function () {
      await this.turnOffEverySetting(1);
    });

    it('should send mail to mailhog', async function () {
      await this.createCustomer(customer);

      const emailsSentTo = await mailhog.getSentAddresses();

      expect(emailsSentTo).to.include(customer.email);
    });

    it('should NOT create customer_new_account_registered_no_password event', async function () {
      await this.turnOffEverySetting(1);

      await this.createCustomer(customer);

      const event = await this.db
        .select()
        .from(this.getTableName('emarsys_events_data'))
        .where({ event_type: 'customer_new_account_registered_no_password' })
        .first();

      const emailsSentTo = await mailhog.getSentAddresses();

      expect(emailsSentTo).to.include(customer.email);
      expect(event).to.be.undefined;
    });

    it('should NOT create customer_new_account_registered event', async function () {
      await this.turnOffEverySetting(1);

      await this.createCustomer(customer, 'Password1234');

      const event = await this.db
        .select()
        .from(this.getTableName('emarsys_events_data'))
        .where({ event_type: 'customer_new_account_registered' })
        .first();

      const emailsSentTo = await mailhog.getSentAddresses();

      expect(emailsSentTo).to.include(customer.email);
      expect(event).to.be.undefined;
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

      it('should NOT create customer_new_account_confirmation event', async function () {
        await this.turnOffEverySetting(1);

        await this.createCustomer(customer, 'Password1234');

        const event = await this.db
          .select()
          .from(this.getTableName('emarsys_events_data'))
          .where({ event_type: 'customer_new_account_confirmation' })
          .first();

        const emailsSentTo = await mailhog.getSentAddresses();

        expect(emailsSentTo).to.include(customer.email);
        expect(event).to.be.undefined;
      });
    });

    it('should NOT create customer_password_reset_confirmation event', async function () {
      await this.turnOffEverySetting(1);

      await this.magentoApi.put({
        path: '/index.php/rest/V1/customers/password',
        payload: {
          email: this.customer.email,
          template: 'email_reset',
          websiteId: this.customer.website_id
        }
      });

      const event = await this.db
        .select()
        .from(this.getTableName('emarsys_events_data'))
        .where({ event_type: 'customer_password_reset_confirmation' })
        .first();

      const emailsSentTo = await mailhog.getSentAddresses();

      expect(emailsSentTo).to.include(this.customer.email);
      expect(event).to.be.undefined;
    });

    it('should NOT create customer_password_reminder event', async function () {
      await this.turnOffEverySetting(1);

      await this.magentoApi.put({
        path: '/index.php/rest/V1/customers/password',
        payload: {
          email: this.customer.email,
          template: 'email_reminder',
          websiteId: this.customer.website_id
        }
      });

      const event = await this.db
        .select()
        .from(this.getTableName('emarsys_events_data'))
        .where({ event_type: 'customer_password_reminder' })
        .first();

      const emailsSentTo = await mailhog.getSentAddresses();

      expect(emailsSentTo).to.include(this.customer.email);
      expect(event).to.be.undefined;
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

        it('should NOT create newsletter_send_confirmation_success_email event', async function () {
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

          const emailsSentTo = await mailhog.getSentAddresses();

          expect(emailsSentTo).to.include(subscriber.email);
          expect(event).to.be.undefined;
        });

        it('should NOT create newsletter_send_unsubscription_email event', async function () {
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

          const emailsSentTo = await mailhog.getSentAddresses();

          expect(emailsSentTo).to.include(subscriber.email);
          expect(event).to.be.undefined;
        });
      });

      context('is enabled', function () {
        before(async function () {
          await this.db
            .insert({ scope: 'default', scope_id: 0, path: 'newsletter/subscription/confirm', value: 1 })
            .into(this.getTableName('core_config_data'));

          // this is for invalidating config cache
          await this.magentoApi.execute('config', 'set', {
            websiteId: 1,
            config: {
              collectMarketingEvents: 'disabled'
            }
          });
        });

        after(async function () {
          await this.db.raw(
            `DELETE FROM ${this.getTableName('core_config_data')} WHERE path="newsletter/subscription/confirm"`
          );
        });

        it('should NOT create newsletter_send_confirmation_request_email event', async function () {
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

          const emailsSentTo = await mailhog.getSentAddresses();

          expect(emailsSentTo).to.include(subscriber.email);
          expect(event).to.be.undefined;
        });

        it('should NOT create newsletter_send_unsubscription_email event', async function () {
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

          const emailsSentTo = await mailhog.getSentAddresses();

          expect(emailsSentTo).to.include(subscriber.email);
          expect(event).to.be.undefined;
        });
      });
    });
  });
});

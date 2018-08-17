'use strict';

const axios = require('axios');
const FormData = require('form-data');

const customer = {
  group_id: 0,
  dob: '1977-11-12',
  email: 'yolo@yolo.net',
  firstname: 'Yolo',
  lastname: 'World',
  store_id: 1,
  website_id: 1,
  disable_auto_group_change: 0
};

const resetPasswordResetRequestEvent = async db => {
  return await db.truncate('password_reset_request_event');
};

describe('Marketing events', function() {
  context('customer_new_account_registered_no_password', function() {
    afterEach(async function() {
      await resetPasswordResetRequestEvent(this.db);
      await this.db.raw('DELETE FROM customer_entity where email = "yolo@yolo.net"');
    });

    it('should create event if collectMarketingEvents is enabled', async function() {
      await this.magentoApi.setConfig({ websiteId: 1, config: { collectMarketingEvents: 'enabled' } });
      await this.createCustomer(customer);

      const events = await this.db.select().from('emarsys_events');

      expect(events.length).to.be.equal(1);

      const event = events[0];
      expect(event.event_type).to.be.equal('customer_new_account_registered_no_password');

      const eventData = JSON.parse(event.event_data);
      expect(eventData.customer.email).to.eql(customer.email);
    });

    it('should create event if collectMarketingEvents is disabled', async function() {
      await this.magentoApi.setDefaultConfig(1);

      await this.createCustomer(customer);

      const event = await this.db
        .select()
        .from('emarsys_events')
        .where({ event_type: 'customer_new_account_registered_no_password' })
        .first();

      expect(event).to.be.undefined;
    });
  });

  context('customer_new_account_registered', function() {
    afterEach(async function() {
      await resetPasswordResetRequestEvent(this.db);
      await this.db.raw('DELETE FROM customer_entity where email = "yolo@yolo.net"');
    });

    it('should create event if collectMarketingEvents is enabled', async function() {
      await this.magentoApi.setConfig({ websiteId: 1, config: { collectMarketingEvents: 'enabled' } });
      await this.createCustomer(customer, 'Password1234');

      const events = await this.db.select().from('emarsys_events');

      expect(events.length).to.be.equal(1);

      const event = events[0];
      expect(event.event_type).to.be.equal('customer_new_account_registered');

      const eventData = JSON.parse(event.event_data);
      expect(eventData.customer.email).to.eql(customer.email);
    });

    it('should create event if collectMarketingEvents is disabled', async function() {
      await this.magentoApi.setDefaultConfig(1);

      await this.createCustomer(customer, 'Password1234');

      const event = await this.db
        .select()
        .from('emarsys_events')
        .where({ event_type: 'customer_new_account_registered' })
        .first();

      expect(event).to.be.undefined;
    });
  });

  context('customer_password_reset_confirmation', function() {
    afterEach(async function() {
      await resetPasswordResetRequestEvent(this.db);
    });

    it('should create an event if collectMarketingEvents turned on', async function() {
      await this.magentoApi.setConfig({ websiteId: 1, config: { collectMarketingEvents: 'enabled' } });

      await this.magentoApi.put({
        path: '/index.php/rest/V1/customers/password',
        payload: {
          email: this.customer.email,
          template: 'email_reset',
          websiteId: this.customer.website_id
        }
      });

      const events = await this.db.select().from('emarsys_events');

      expect(events.length).to.be.equal(1);

      const event = events[0];

      expect(event.event_type).to.be.equal('customer_password_reset_confirmation');

      const eventData = JSON.parse(event.event_data);
      expect(eventData.customer.email).to.equal(this.customer.email);
    });

    it('should not create an event if collectMarketingEvents turned off', async function() {
      await this.magentoApi.setDefaultConfig(1);

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
        .from('emarsys_events')
        .where({ event_type: 'customer_password_reset_confirmation' })
        .first();

      expect(event).to.be.undefined;
    });
  });

  context('customer_password_reminder', function() {
    afterEach(async function() {
      await resetPasswordResetRequestEvent(this.db);
    });

    it('should create an event if collectMarketingEvents turned on', async function() {
      await this.magentoApi.setConfig({ websiteId: 1, config: { collectMarketingEvents: 'enabled' } });

      await this.magentoApi.put({
        path: '/index.php/rest/V1/customers/password',
        payload: {
          email: this.customer.email,
          template: 'email_reminder',
          websiteId: this.customer.website_id
        }
      });

      const events = await this.db.select().from('emarsys_events');

      expect(events.length).to.be.equal(1);

      const event = events[0];

      expect(event.event_type).to.be.equal('customer_password_reminder');

      const eventData = JSON.parse(event.event_data);
      expect(eventData.customer.email).to.equal(this.customer.email);
    });

    it('should not create an event if collectMarketingEvents turned off', async function() {
      await this.magentoApi.setDefaultConfig(1);

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
        .from('emarsys_events')
        .where({ event_type: 'customer_password_reminder' })
        .first();

      expect(event).to.be.undefined;
    });
  });

  context.skip('customer_email_change', function() {
    it('should create an event if collectMarketingEvents turned on', async function() {
      await this.magentoApi.setConfig({ websiteId: 1, config: { collectMarketingEvents: 'enabled' } });

      const newEmailAddress = 'passwordChangeOnTest@yolo.net';
      await this.magentoApi.put({
        path: `/index.php/rest/V1/customers/${this.customer.entityId}`,
        payload: {
          customer: {
            email: newEmailAddress,
            id: this.customer.entityId,
            firstname: this.customer.firstname,
            lastname: this.customer.lastname,
            websiteId: this.customer.website_id
          },
          password: this.customer.password
        }
      });

      const events = await this.db.select().from('emarsys_events');

      expect(events.length).to.be.equal(1);

      const event = events[0];

      expect(event.event_type).to.be.equal('customer_email_change');

      const eventData = JSON.parse(event.event_data);
      expect(eventData.customer.email).to.equal(newEmailAddress);
      this.customer.email = newEmailAddress;
    });

    it('should not create an event if collectMarketingEvents turned off', async function() {
      await this.magentoApi.setDefaultConfig(1);

      const newEmailAddress = 'passwordChangeOffTest@yolo.net';
      await this.magentoApi.put({
        path: `/index.php/rest/V1/customers/${this.customer.entityId}`,
        payload: {
          customer: {
            email: newEmailAddress,
            id: this.customer.entityId,
            firstname: this.customer.firstname,
            lastname: this.customer.lastname,
            websiteId: this.customer.website_id
          },
          password: this.customer.password
        }
      });

      const event = await this.db
        .select()
        .from('emarsys_events')
        .where({ event_type: 'customer_email_change' })
        .first();

      expect(event).to.be.undefined;
      this.customer.email = newEmailAddress;
    });
  });

  context.skip('customer_password_reset', function() {
    afterEach(async function() {
      await resetPasswordResetRequestEvent(this.db);
    });

    it('should create an event if collectMarketingEvents turned on', async function() {
      await this.magentoApi.setConfig({ websiteId: 1, config: { collectMarketingEvents: 'enabled' } });

      const newPassword = 'Almafa1234';
      await this.magentoApi.put({
        path: `/index.php/rest/V1/customers/me/password?customerId=${this.customer.entityId}`,
        payload: {
          currentPassword: this.customer.password,
          newPassword
        }
      });

      const events = await this.db.select().from('emarsys_events');

      expect(events.length).to.be.equal(1);

      const event = events[0];

      expect(event.event_type).to.be.equal('customer_password_reset');

      const eventData = JSON.parse(event.event_data);
      expect(eventData.customer.email).to.equal(this.customer.email);
      this.customer.password = newPassword;
    });

    it('should not create an event if collectMarketingEvents turned off', async function() {
      await this.magentoApi.setDefaultConfig(1);

      const newPassword = 'Almafa4567';
      await this.magentoApi.put({
        path: `/index.php/rest/V1/customers/me/password?customerId=${this.customer.entityId}`,
        payload: {
          currentPassword: this.customer.password,
          newPassword
        }
      });

      const event = await this.db
        .select()
        .from('emarsys_events')
        .where({ event_type: 'customer_password_reset' })
        .first();

      expect(event).to.be.undefined;
      this.customer.password = newPassword;
    });
  });

  context.skip('newsletter_send_confirmation_success_email', function() {
    it('should create an event if collectMarketingEvents turned on', async function() {
      await this.magentoApi.setConfig({ websiteId: 1, config: { collectMarketingEvents: 'enabled' } });

      const subscriberEmail = 'test@emarsys.com';
      const formData = new FormData();
      formData.append('email', subscriberEmail);

      await axios
        .post('http://magento.local/index.php/newsletter/subscriber/new/', formData, {
          maxRedirects: 0,
          headers: formData.getHeaders()
        })
        .catch(error => {
          console.log(error);
          if (error.response.status !== 302) throw error;
        });

      const alma = await this.db.select().from('newsletter_subscriber');
      console.log(alma);

      const events = await this.db.select().from('emarsys_events');

      console.log('events', events);
      expect(events.length).to.be.equal(1);

      const event = events[0];
      expect(event.event_type).to.be.equal('newsletter_send_confirmation_success_email');

      const eventData = JSON.parse(event.event_data);
      expect(eventData.subscriber.subscriber_email).to.equal(subscriberEmail);
    });

    it('should not create an event if collectMarketingEvents turned off', async function() {
      await this.magentoApi.setDefaultConfig(1);

      const subscriberEmail = 'test@emarsys.com';
      const formData = new FormData();
      formData.append('email', subscriberEmail);

      await axios
        .post('http://magento.local/index.php/newsletter/subscriber/new/', formData, {
          maxRedirects: 0,
          headers: formData.getHeaders()
        })
        .catch(error => {
          if (error.response.status !== 302) throw error;
        });

      const event = await this.db
        .select()
        .from('emarsys_events')
        .where({ event_type: 'newsletter_send_confirmation_success_email' })
        .first();

      expect(event).to.be.undefined;
    });
  });
});

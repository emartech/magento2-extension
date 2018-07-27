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

describe('Marketing events', function() {
  context.skip('customer_new_account_registered_no_password', function() {
    afterEach(async function() {
      await this.db.raw('DELETE FROM customer_entity where email = "yolo@yolo.net"');
    });

    it('should create event if collectMarketingEvents is enabled', async function() {
      await this.magentoApi.setSettings({ collectMarketingEvents: 'enabled' });
      await this.createCustomer(customer);

      const events = await this.db.select().from('emarsys_events');

      expect(events.length).to.be.equal(1);

      const event = events[0];
      expect(event.event_type).to.be.equal('customer_new_account_registered_no_password');

      const eventData = JSON.parse(event.event_data);
      expect(eventData.customer.email).to.eql(customer.email);
    });

    it('should create event if collectMarketingEvents is disabled', async function() {
      await this.createCustomer(customer);

      const event = await this.db
        .select()
        .from('emarsys_events')
        .where({ event_type: 'customer_new_account_registered_no_password' })
        .first();

      expect(event).to.be.undefined;
    });
  });

  context.skip('customer_password_reset_confirmation', function() {
    it('should create an event if collectMarketingEvents turned on', async function() {
      await this.magentoApi.setSettings({ collectMarketingEvents: 'enabled' });

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

  context.skip('customer_password_reminder', function() {
    it('should create an event if collectMarketingEvents turned on', async function() {
      await this.magentoApi.setSettings({ collectMarketingEvents: 'enabled' });

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

  context.skip('newsletter_send_confirmation_success_email', function() {
    it('should create an event if collectMarketingEvents turned on', async function() {
      await this.magentoApi.setSettings({ collectMarketingEvents: 'enabled' });

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

      const events = await this.db.select().from('emarsys_events');

      expect(events.length).to.be.equal(1);

      const event = events[0];
      expect(event.event_type).to.be.equal('newsletter_send_confirmation_success_email');

      const eventData = JSON.parse(event.event_data);
      expect(eventData.subscriber.subscriber_email).to.equal(subscriberEmail);
    });

    it('should not create an event if collectMarketingEvents turned off', async function() {
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
        .where({ event_type: 'newsletter_send_confirmation_success_email' })
        .first();

      expect(event).to.be.undefined;
    });
  });
});

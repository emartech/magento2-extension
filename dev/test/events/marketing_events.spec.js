'use strict';

const axios = require('axios');

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

      const event = await this.db
        .select()
        .from('emarsys_events')
        .where({ event_type: 'customer_new_account_registered_no_password' })
        .first();

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
    before(async function() {
      await this.db.truncate('emarsys_events');
    });

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

      const event = await this.db
        .select()
        .from('emarsys_events')
        .first();

      expect(event.event_type).to.be.equal('customer_password_reset_confirmation');

      const eventData = JSON.parse(event.event_data);
      expect(eventData.email).to.equal(this.customer.email);
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
        .first();

      expect(event).to.be.undefined;
    });
  });
});

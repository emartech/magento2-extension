'use strict';

const Magento2ApiClient = require('@emartech/magento2-api');

let magentoApi;

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
  before(function() {
    magentoApi = new Magento2ApiClient({
      baseUrl: 'http://web',
      token: this.token
    });
  });

  afterEach(async function() {
    await this.db.raw('DELETE FROM customer_entity where email = "yolo@yolo.net"');
  });

  it.skip('are saved in DB if collectMarketingEvents is enabled', async function() {
    await magentoApi.setSettings({ collectMarketingEvents: 'enabled' });
    await this.createCustomer(customer);

    const event = await this.db
      .select()
      .from('emarsys_events')
      .where({ event_type: 'customer_new_account_registered_no_password' })
      .first();

    const eventData = JSON.parse(event.event_data);
    expect(eventData.customer.email).to.eql(customer.email);
  });

  it.skip('are not saved in DB if collectMarketingEvents is disabled', async function() {
    await this.createCustomer(customer);

    const event = await this.db
      .select()
      .from('emarsys_events')
      .where({ event_type: 'customer_new_account_registered_no_password' })
      .first();

    expect(event).to.be.undefined;
  });
});

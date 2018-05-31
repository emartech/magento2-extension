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

describe('Customer events', function() {
  before(function() {
    magentoApi = new Magento2ApiClient({
      baseUrl: 'http://web',
      token: this.token
    });
  });

  it('are saved in DB if collectCustomerEvents is enabled', async function() {
    await magentoApi.setSettings({ collectCustomerEvents: 'enabled' });
    await magentoApi.post({ path: '/index.php/rest/V1/customers', payload: { customer } });

    const event = await this.db
      .select()
      .from('emarsys_events')
      .where({ event_type: 'customer_account' })
      .first();

    const eventData = JSON.parse(event.event_data);
    expect(eventData.email).to.eql(customer.email);
  });

  it('are not saved in DB if collectCustomerEvents is disabled', async function() {
    await magentoApi.post({ path: '/index.php/rest/V1/customers', payload: { customer } });

    const event = await this.db
      .select()
      .from('emarsys_events')
      .where({ event_type: 'customer_account' })
      .first();

    expect(event).to.be.undefined;
  });
});

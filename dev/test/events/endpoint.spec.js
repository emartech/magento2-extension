'use strict';

const Magento2ApiClient = require('@emartech/magento2-api');

let magentoApi;

const customers = [
  {
    group_id: 0,
    dob: '1977-11-12',
    email: 'yolo@yolo.net',
    firstname: 'Yolo',
    lastname: 'World',
    store_id: 1,
    website_id: 1,
    disable_auto_group_change: 0
  },
  {
    group_id: 0,
    dob: '1977-11-12',
    email: 'doggo@yolo.net',
    firstname: 'Doggo',
    lastname: 'World',
    store_id: 1,
    website_id: 1,
    disable_auto_group_change: 0
  },
  {
    group_id: 0,
    dob: '1977-11-12',
    email: 'pupper@yolo.net',
    firstname: 'Pupper',
    lastname: 'World',
    store_id: 1,
    website_id: 1,
    disable_auto_group_change: 0
  }
];

describe('Events API endpoint', function() {
  before(function() {
    magentoApi = new Magento2ApiClient({
      baseUrl: 'http://web',
      token: this.token
    });
  });

  afterEach(async function() {
    await this.db.raw(
      'DELETE FROM customer_entity where email in ("yolo@yolo.net", "doggo@yolo.net", "pupper@yolo.net")'
    );
  });

  it('returns number of events defined in page_size and deletes events before since_id', async function() {
    const pageSize = 2;
    await magentoApi.setSettings({ collectCustomerEvents: 'enabled' });
    for (const customer of customers) {
      await this.createCustomer(customer);
    }

    const eventsResponse = await magentoApi.execute('events', 'getSince', 0, pageSize);

    expect(eventsResponse.events.length).to.equal(pageSize);
    expect(eventsResponse.last_page).to.equal(3);

    let sinceId = eventsResponse.events.pop().event_id;

    const secondEventsResponse = await magentoApi.execute('events', 'getSince', sinceId, pageSize);

    expect(secondEventsResponse.last_page).to.equal(2);

    const eventsInDb = await this.db.select().from('emarsys_events');

    expect(eventsInDb.length).to.equal(4);
  });
});

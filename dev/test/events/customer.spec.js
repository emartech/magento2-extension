'use strict';

const customer = {
  group_id: 0,
  dob: '1977-11-12',
  email: 'yolo99@yolo.net',
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

describe('Customer events', function() {
  before(async function() {
    await this.magentoApi.execute('attributes', 'set', {
      websiteId: 1,
      type: 'customer',
      attributeCodes: ['emarsys_test_favorite_car']
    });
  });

  afterEach(async function() {
    await this.db.raw(`DELETE FROM ${this.getTableName('customer_entity')} where email = "yolo99@yolo.net"`);
    await this.magentoApi.execute('config', 'setDefault', 1);
  });

  after(async function() {
    await this.magentoApi.execute('attributes', 'set', {
      websiteId: 1,
      type: 'customer',
      attributeCodes: []
    });
  });

  it('are saved in DB if collectCustomerEvents is enabled', async function() {
    await this.magentoApi.execute('config', 'set', { websiteId: 1, config: { collectCustomerEvents: 'enabled' } });
    await this.createCustomer(customer);

    const event = await this.db
      .select()
      .from(this.getTableName('emarsys_events_data'))
      .where({ event_type: 'customers/update' })
      .first();

    const eventData = JSON.parse(event.event_data);
    expect(eventData.email).to.eql(customer.email);
    expect(eventData.extra_fields).to.eql([{ key: 'emarsys_test_favorite_car', value: 'skoda' }]);
    expect(event.website_id).to.equal(1);
    expect(event.store_id).to.equal(1);
  });

  it('are not saved in DB if collectCustomerEvents is disabled', async function() {
    await this.magentoApi.execute('config', 'setDefault', 1);

    await this.createCustomer(customer);

    const event = await this.db
      .select()
      .from(this.getTableName('emarsys_events_data'))
      .where({ event_type: 'customers/update' })
      .first();

    expect(event).to.be.undefined;
  });
});

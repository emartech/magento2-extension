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
    await this.turnOffEverySetting(1);
  });

  after(async function() {
    await this.magentoApi.execute('attributes', 'set', {
      websiteId: 1,
      type: 'customer',
      attributeCodes: []
    });
  });

  it('"customers/update" is saved in DB if customer is created', async function() {
    await this.magentoApi.execute('config', 'set', { websiteId: 1, config: { collectCustomerEvents: 'enabled' } });
    await this.createCustomer(customer);

    const event = await this.db
      .select()
      .from(this.getTableName('emarsys_events_data'))
      .where({ event_type: 'customers/update' })
      .first();

    const eventData = JSON.parse(event.event_data);
    expect(eventData.email).to.eql(customer.email);
    expect(eventData.extra_fields).to.eql([{ key: 'emarsys_test_favorite_car', value: 'skoda', text_value: null }]);
    expect(event.website_id).to.equal(1);
    expect(event.store_id).to.equal(1);
  });

  it('"customers/update" is saved in DB if customer is updated', async function() {
    const createdCustomer = await this.createCustomer(customer);

    await this.magentoApi.execute('config', 'set', { websiteId: 1, config: { collectCustomerEvents: 'enabled' } });

    try {
      await this.magentoApi.put({
        path: `/rest/V1/customers/${createdCustomer.entityId}`,
        payload: { customer: { ...customer, email: 'yolo100@yolo.net' } }
      });
    } catch (error) {
      console.log(error.response);
    }

    const event = await this.db
      .select()
      .from(this.getTableName('emarsys_events_data'))
      .where({ event_type: 'customers/update' })
      .first();

    const eventData = JSON.parse(event.event_data);
    expect(eventData.email).to.eql('yolo100@yolo.net');
    expect(eventData.extra_fields).to.eql([{ key: 'emarsys_test_favorite_car', value: 'skoda', text_value: null }]);
    expect(event.website_id).to.equal(1);
    expect(event.store_id).to.equal(1);
  });

  it('"customers/delete" is saved in DB if customer is deleted', async function() {
    const createdCustomer = await this.createCustomer(customer);

    await this.magentoApi.execute('config', 'set', { websiteId: 1, config: { collectCustomerEvents: 'enabled' } });

    await this.magentoApi.delete({ path: `/rest/V1/customers/${createdCustomer.entityId}` });

    const event = await this.db
      .select()
      .from(this.getTableName('emarsys_events_data'))
      .where({ event_type: 'customers/delete' })
      .first();
    const eventData = JSON.parse(event.event_data);
    expect(eventData.email).to.eql(customer.email);
    expect(event.entity_id).to.eql(createdCustomer.entityId);
  });

  it('are not saved in DB if collectCustomerEvents is disabled', async function() {
    await this.turnOffEverySetting(1);

    await this.createCustomer(customer);

    const event = await this.db
      .select()
      .from(this.getTableName('emarsys_events_data'))
      .where({ event_type: 'customers/update' })
      .first();

    expect(event).to.be.undefined;
  });
});

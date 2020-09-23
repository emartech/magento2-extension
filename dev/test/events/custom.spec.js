'use strict';

const getLastEvent = async (db) =>
  await db.select().from(`${tablePrefix}emarsys_events_data`).orderBy('event_id', 'desc').first();

const getAllEvents = async (db) =>
  await db.select().from(`${tablePrefix}emarsys_events_data`).orderBy('event_id', 'desc');

let tablePrefix;

describe('Custom events', function () {
  before(function () {
    tablePrefix = this.getTableName('');
  });

  it('should save custom event', async function () {
    await this.magentoApi.execute('config', 'set', { websiteId: 1, config: { collectMarketingEvents: 'enabled' } });

    const eventId = 12345;
    const eventData = {
      customer_email: 'hello@yolo.hu',
      whatever: 'yes'
    };

    const result = await this.triggerCustomEvent({
      eventData,
      eventId
    });

    const eventInDb = await getLastEvent(this.db);

    expect(result.data.status).to.equal(0);
    expect(eventInDb.event_type).to.equal(`custom/${eventId}`);
    expect(JSON.parse(eventInDb.event_data)).to.eql(eventData);
  });

  it('should save custom event for specific website and store', async function () {
    await this.magentoApi.execute('config', 'set', { websiteId: 2, config: { collectMarketingEvents: 'enabled' } });

    const eventId = 12345;
    const storeId = 2;
    const eventData = {
      customer_email: 'hello@yolo.hu',
      whatever: 'yes'
    };

    const result = await this.triggerCustomEvent({
      eventData,
      storeId,
      eventId
    });

    const eventInDb = await getLastEvent(this.db);

    expect(result.data.status).to.equal(0);
    expect(eventInDb.website_id).to.equal(2);
    expect(eventInDb.store_id).to.equal(2);
  });

  it('should NOT save custom event if not enabled for website', async function () {
    await this.magentoApi.execute('config', 'set', { websiteId: 2, config: { collectMarketingEvents: 'disabled' } });

    const eventId = 12345;
    const storeId = 2;
    const eventData = {
      customer_email: 'hello@yolo.hu',
      whatever: 'yes'
    };

    const result = await this.triggerCustomEvent({
      eventData,
      storeId,
      eventId
    });

    const eventInDb = await getLastEvent(this.db);

    expect(result.data.status).to.equal(0);
    expect(eventInDb).to.be.undefined;
  });

  it('should save custom events with unique entity_id', async function () {
    await this.magentoApi.execute('config', 'set', { websiteId: 1, config: { collectMarketingEvents: 'enabled' } });

    const eventId = 12345;
    const eventData = {
      customer_email: 'hello@yolo.hu',
      whatever: 'yes'
    };

    await this.triggerCustomEvent({
      eventData,
      eventId
    });

    await this.triggerCustomEvent({
      eventData,
      eventId
    });

    const eventsInDb = await getAllEvents(this.db);

    expect(eventsInDb[0].entity_id !== eventsInDb[1].entity_id).to.be.true;
  });

  it('should throw error if event_id is missing', async function () {
    await this.magentoApi.execute('config', 'set', { websiteId: 1, config: { collectMarketingEvents: 'enabled' } });

    const eventData = {
      customer_email: 'hello@yolo.hu',
      whatever: 'yes'
    };

    const result = await this.triggerCustomEvent({
      eventData,
      eventId: null
    });

    expect(result.data.status).to.equal(1);
    expect(result.data.error[2]).to.equal('  The "--id" option requires a value.');
  });

  it('should throw error if customer_email is missing from event_data', async function () {
    await this.magentoApi.execute('config', 'set', { websiteId: 1, config: { collectMarketingEvents: 'enabled' } });

    const eventId = 12345;
    const eventData = {
      whatever: 'yes'
    };

    const result = await this.triggerCustomEvent({
      eventData,
      eventId
    });

    expect(result.data.status).to.equal(1);
    expect(result.data.error[4]).to.equal('  customer_email is required in event_data');
  });
});

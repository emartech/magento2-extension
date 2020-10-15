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

  afterEach(async function () {
    await this.db.truncate(this.getTableName('emarsys_events_data'));
  });

  after(async function () {
    await this.magentoApi.execute('config', 'set', { websiteId: 1, config: { collectMarketingEvents: 'disabled' } });

    await this.magentoApi.execute('config', 'set', {
      websiteId: 2,
      config: {
        collectMarketingEvents: 'disabled',
        storeSettings: []
      }
    });
  });

  it('should save custom event', async function () {
    await this.magentoApi.execute('config', 'set', { websiteId: 1, config: { collectMarketingEvents: 'enabled' } });

    const eventId = 12345;
    const eventData = {
      customerEmail: 'hello@yolo.hu',
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
    await this.magentoApi.execute('config', 'set', {
      websiteId: 2,
      config: {
        collectMarketingEvents: 'enabled',
        storeSettings: [
          { store_id: 0, slug: 'testadminslug' },
          { store_id: 2, slug: 'test2slug' }
        ]
      }
    });

    const eventId = 12345;
    const storeId = 2;
    const eventData = {
      customerEmail: 'hello@yolo.hu',
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
    await this.magentoApi.execute('config', 'set', {
      websiteId: 2,
      config: {
        collectMarketingEvents: 'disabled',
        storeSettings: [
          { store_id: 0, slug: 'testadminslug' },
          { store_id: 2, slug: 'test2slug' }
        ]
      }
    });

    const eventId = 12345;
    const storeId = 2;
    const eventData = {
      customerEmail: 'hello@yolo.hu',
      whatever: 'yes'
    };

    const result = await this.triggerCustomEvent({
      eventData,
      storeId,
      eventId
    });

    const eventInDb = await getLastEvent(this.db);

    expect(result.data.status).to.equal(1);
    if (!this.magentoVersion.startsWith('2.2')) {
      expect(result.data.error[4]).to.equal('  marketing events are not enabled for store (ID: 2)');
      expect(eventInDb).to.be.undefined;
    }
  });

  it('should not merge custom events', async function () {
    await this.magentoApi.execute('config', 'set', { websiteId: 1, config: { collectMarketingEvents: 'enabled' } });

    const eventId = 12345;
    const eventData = {
      customerEmail: 'hello@yolo.hu',
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

    expect(eventsInDb.length).to.equal(2);
  });

  it('should throw error if event_id is missing', async function () {
    await this.magentoApi.execute('config', 'set', { websiteId: 1, config: { collectMarketingEvents: 'enabled' } });

    const eventData = {
      customerEmail: 'hello@yolo.hu',
      whatever: 'yes'
    };

    const result = await this.triggerCustomEvent({
      eventData,
      eventId: null
    });

    const errorMessage = this.magentoVersion.startsWith('2.2') ? result.data.error[3] : result.data.error[2];

    expect(result.data.status).to.equal(1);
    expect(errorMessage).to.equal('  The "--id" option requires a value.');
  });

  it('should throw error if customerEmail is missing from event_data', async function () {
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
    expect(result.data.error[4]).to.equal('  customerEmail is required in event_data');
  });
});

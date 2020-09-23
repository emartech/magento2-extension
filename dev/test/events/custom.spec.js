'use strict';

const getLastEvent = async (db) =>
  await db.select().from(`${tablePrefix}emarsys_events_data`).orderBy('event_id', 'desc').first();

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
});

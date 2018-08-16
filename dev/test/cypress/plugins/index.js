'use strict';

// ***********************************************************
// This example plugins/index.js can be used to load plugins
//
// You can change the location of this file or turn off loading
// the plugins file with the 'pluginsFile' configuration option.
//
// You can read more here:
// https://on.cypress.io/plugins-guide
// ***********************************************************

// This function is called when a project is opened or re-opened (e.g. due to
// the project's config changing)

const knex = require('knex');
const Magento2ApiClient = require('@emartech/magento2-api');

const db = knex({
  client: 'mysql',
  connection: {
    host: process.env.MYSQL_HOST,
    user: process.env.MYSQL_USER,
    password: process.env.MYSQL_PASSWORD,
    database: process.env.MYSQL_TEST_DATABASE
  }
});

let magentoToken = null;
const getMagentoToken = async () => {
  if (!magentoToken) {
    const result = await db
      .select('value')
      .from('core_config_data')
      .where({ path: 'emartech/emarsys/connecttoken' })
      .first();

    const { token } = JSON.parse(Buffer.from(result.value, 'base64'));
    magentoToken = token;
  }
  return magentoToken;
};

const getMagentoApi = async () => {
  const token = await getMagentoToken();

  return new Magento2ApiClient({
    baseUrl: 'http://web',
    token
  });
};

module.exports = (on, config) => { // eslint-disable-line no-unused-vars
  // `on` is used to hook into various events Cypress emits
  // `config` is the resolved Cypress config

  on('task', {
    clearDb: async () => {
      await db.truncate('emarsys_events');
      // await db.raw('delete from newsletter_subscriber');
      return true;
    },
    setConfig: async ({ websiteId = 1, config = {} }) => {
      const magentoApi = await getMagentoApi();
      const response = await magentoApi.setConfig({ websiteId, config });
      if (response.data !== 'OK') {
        throw new Error('Magento config set failed!');
      }
      return response.data;
    },
    getEventTypeFromDb: async (eventType) => {
      const event = await db
        .select()
        .from('emarsys_events')
        .where({
          event_type: eventType
        })
        .first();

      event.event_data = JSON.parse(event.event_data);
      return event;
    },
    getAllEvents: async () => {
      return await db.select().from('emarsys_events');
    }
  });
};

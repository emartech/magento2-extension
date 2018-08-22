'use strict';

const knex = require('knex');
const Magento2ApiClient = require('@emartech/magento2-api');

const getDbConnectionConfig = () => {
  if (process.env.CYPRESS_baseUrl) {
    return {
      host: '127.0.0.1',
      port: 13306,
      user: 'magento',
      password: 'magento',
      database: 'magento_test'
    };
  }
  return {
    host: process.env.MYSQL_HOST,
    user: process.env.MYSQL_USER,
    password: process.env.MYSQL_PASSWORD,
    database: process.env.MYSQL_TEST_DATABASE
  };
};

const db = knex({
  client: 'mysql',
  connection: getDbConnectionConfig()
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
    baseUrl: process.env.CYPRESS_baseUrl || 'http://magento-test.local',
    token
  });
};

let defaultCustomer = null;
const createCustomer = async (customer, password) => {
  const magentoApi = await getMagentoApi();
  await magentoApi.post({ path: '/index.php/rest/V1/customers', payload: { customer, password } });

  const { entity_id: entityId } = await db
    .select('entity_id')
    .from('customer_entity')
    .where({ email: customer.email })
    .first();

  return Object.assign({}, customer, { entityId, password });
};

const clearEvents = async () => {
  return await db.truncate('emarsys_events');
};

module.exports = (on, config) => { // eslint-disable-line no-unused-vars
  // `on` is used to hook into various events Cypress emits
  // `config` is the resolved Cypress config

  on('task', {
    clearDb: async () => {
      await clearEvents();
      return true;
    },
    clearEvents: async () => {
      await clearEvents();
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
    },
    createCustomer: async ({ customer }) => {
      return await createCustomer(customer, 'Password1234');
    },
    getDefaultCustomer: async () => {
      if (!defaultCustomer) {
        const customer = {
          group_id: 0,
          dob: '1977-11-12',
          email: 'default@cypress.com',
          firstname: 'Cypress',
          lastname: 'Default',
          store_id: 1,
          website_id: 1,
          disable_auto_group_change: 0
        };
        defaultCustomer = await createCustomer(customer, 'Password1234');
        await clearEvents();
      }
      return defaultCustomer;
    },
    getCoreConfig: async () => {
      const config = await db.select().from('core_config_data').where('path', 'like', '%emartech%');
      console.log(config);
      return true;
    }
  });
};

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
    database: process.env.MYSQL_DATABASE
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
    console.log('MAGENTO-TOKEN', magentoToken);
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
  return await db.truncate('emarsys_events_data');
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

      if (response.data.status !== 'ok') {
        throw new Error('Magento config set failed!');
      }
      return response.data;
    },
    getEventTypeFromDb: async (eventType) => {
      const event = await db
        .select()
        .from('emarsys_events_data')
        .where({
          event_type: eventType
        })
        .first();

      if (!event) {
        return null;
      }

      event.event_data = JSON.parse(event.event_data);
      return event;
    },
    getAllEvents: async () => {
      return await db.select().from('emarsys_events_data');
    },
    createCustomer: async ({ customer }) => {
      return await createCustomer(customer, 'Password1234');
    },
    getDefaultCustomer: async () => {
      if (!defaultCustomer) {
        const customer = {
          group_id: 0,
          dob: '1977-11-12',
          email: 'cypress@default.com',
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
    log: (logObject) => {
      console.log('LOG', logObject);
      return true;
    },
    setDefaultCustomerProperty: (customerData) => {
      defaultCustomer = Object.assign({}, defaultCustomer, customerData);
      return defaultCustomer;
    },
    getSubscription: async (email) => {
      return await db
        .select()
        .from('newsletter_subscriber')
        .where({ subscriber_email: email })
        .first();
    },
    setDoubleOptin: async (stateOn) => {
      if (stateOn) {
        return await db
          .insert({
            scope: 'default',
            scope_id: 0,
            path: 'newsletter/subscription/confirm',
            value: 1
          })
          .into('core_config_data');
      } else {
        return await db('core_config_data')
          .where({ path: 'newsletter/subscription/confirm' })
          .delete();
      }
    }
  });
};

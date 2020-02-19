'use strict';

const knex = require('knex');
const Magento2ApiClient = require('@emartech/magento2-api');

const getTableName = table => `${process.env.TABLE_PREFIX || ''}${table}`;

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

let db = null;
const getDb = () => {
  if (!db) {
    db = knex({
      client: 'mysql',
      connection: getDbConnectionConfig()
    });
  }
  return db;
};

const getMagentoApi = () => {
  return new Magento2ApiClient({
    baseUrl: process.env.CYPRESS_baseUrl || 'http://magento-test.local',
    token: 'Almafa456'
  });
};

let magentoVersion = null;
const getMagentoVersion = async () => {
  const magentoApi = getMagentoApi();
  const result = await magentoApi.execute('systeminfo', 'get');
  magentoVersion = result.magento_version;
};

let defaultCustomer = null;
const createCustomer = async (customer, password) => {
  const magentoApi = getMagentoApi();
  await magentoApi.post({ path: '/index.php/rest/V1/customers', payload: { customer, password } });

  const { entity_id: entityId } = await getDb()
    .select('entity_id')
    .from(getTableName('customer_entity'))
    .where({ email: customer.email })
    .first();

  return Object.assign({}, customer, { entityId, password });
};

const clearEvents = async () => {
  return await getDb().truncate(getTableName('emarsys_events_data'));
};

const flushMagentoCache = async () => {
  const magentoApi = getMagentoApi();
  return await magentoApi.get({ path: '/cache-flush.php' });
};

// eslint-disable-next-line no-unused-vars
module.exports = (on, config) => {
  on('task', {
    clearEvents: async () => {
      await clearEvents();
      return true;
    },
    disableEmail: async () => {
      await getDb()
        .insert({
          scope: 'default',
          scope_id: '0',
          path: 'system/smtp/disable',
          value: 1
        })
        .into(getTableName('core_config_data'));

      return await flushMagentoCache();
    },
    enableEmail: async () => {
      const db = getDb();
      await db(getTableName('core_config_data'))
        .where({ path: 'system/smtp/disable' })
        .delete();

      return await flushMagentoCache();
    },
    setConfig: async ({
      websiteId = 1,
      collectMarketingEvents = 'disabled',
      injectSnippet = 'disabled',
      merchantId = null,
      webTrackingSnippetUrl = null
    }) => {
      const magentoApi = getMagentoApi();
      const config = {
        websiteId,
        config: {
          collectMarketingEvents,
          injectSnippet,
          merchantId,
          webTrackingSnippetUrl,
          storeSettings: [
            {
              storeId: 0,
              slug: 'cypress-testadminslug'
            },
            {
              storeId: 1,
              slug: 'cypress-testslug'
            }
          ]
        }
      };

      const response = await magentoApi.execute('config', 'set', config);

      if (response.data.status !== 'ok') {
        throw new Error('Magento config set failed!');
      }
      return response.data;
    },
    getMagentoVersion: async () => {
      if (!magentoVersion) {
        await getMagentoVersion();
      }
      return magentoVersion;
    },
    getEventTypeFromDb: async eventType => {
      const event = await getDb()
        .select()
        .from(getTableName('emarsys_events_data'))
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
      return await getDb()
        .select()
        .from(getTableName('emarsys_events_data'));
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
    log: logObject => {
      console.log('LOG', logObject);
      return true;
    },
    setDefaultCustomerProperty: customerData => {
      defaultCustomer = Object.assign({}, defaultCustomer, customerData);
      return defaultCustomer;
    },
    getSubscription: async email => {
      return await getDb()
        .select()
        .from(getTableName('newsletter_subscriber'))
        .where({ subscriber_email: email })
        .first();
    },
    setDoubleOptin: async stateOn => {
      if (stateOn) {
        return await getDb()
          .insert({
            scope: 'default',
            scope_id: 0,
            path: 'newsletter/subscription/confirm',
            value: 1
          })
          .into(getTableName('core_config_data'));
      } else {
        return await getDb()(getTableName('core_config_data'))
          .where({ path: 'newsletter/subscription/confirm' })
          .delete();
      }
    },
    flushMagentoCache: async () => {
      return await flushMagentoCache();
    }
  });
};

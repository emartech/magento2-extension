'use strict';

const Magento2ApiClient = require('@itg-commerce/magento2-api');
const db = require('../../helpers/db');
const { getTableName, cacheTablePrefix } = require('../../helpers/get-table-name');
const { getSentAddresses, clearMails } = require('../../helpers/mailhog');
const axios = require('axios');

// API bearer token compatibility
db('information_schema.tables')
    .select('table_name')
    .where('table_name', 'like', '%core_config_data')
    .then((rows) => {
        const { table_name: tableName } = rows[0];
        let prefix = tableName.split('core_config_data')[0];
        let replaceQuery = db(prefix + 'core_config_data')
            .insert({ scope: 'default', scope_id: 0, path: 'oauth/consumer/enable_integration_as_bearer', value: '1' })
            .toString().replace(/^INSERT/i, 'REPLACE');

        db.raw(replaceQuery).then(() => {
            let _baseUrl = process.env.CYPRESS_baseUrl || 'http://magento-test.local';
            axios.get(`${_baseUrl}/cache-flush.php`);
        });
    });
// END API bearer token compatibility

const magentoApi = new Magento2ApiClient({
  baseUrl: process.env.CYPRESS_baseUrl || 'http://magento-test.local',
  token: 'Almafa456'
});

let magentoVersion = null;
const getMagentoVersion = async () => {
  const result = await magentoApi.execute('systeminfo', 'get');
  magentoVersion = result.magento_version;
};

let defaultCustomer = null;
const createCustomer = async (customer, password) => {
  await magentoApi.post({ path: '/index.php/rest/V1/customers', payload: { customer, password } });

  const { entity_id: entityId } = await db
    .select('entity_id')
    .from(getTableName('customer_entity'))
    .where({ email: customer.email })
    .first();

  return { ...customer, entityId, password };
};

const clearEvents = () => db.truncate(getTableName('emarsys_events_data'));

// eslint-disable-next-line no-unused-vars
module.exports = (on, config) => {
  on('task', {
    clearEvents,
    cacheTablePrefix,
    clearMails: async () => {
      await clearMails();
      return true;
    },
    getSentAddresses,
    flushMagentoCache: () => magentoApi.get({ path: '/cache-flush.php' }),
    enableEmail: () => {
      return db(getTableName('core_config_data'))
        .where({ path: 'system/smtp/disable' })
        .delete();
    },
    setConfig: async ({
      websiteId = 1,
      collectMarketingEvents = 'disabled',
      magentoSendEmail = 'disabled',
      injectSnippet = 'disabled',
      merchantId = '',
      webTrackingSnippetUrl = ''
    }) => {
      const config = {
        websiteId,
        config: {
          collectMarketingEvents,
          injectSnippet,
          magentoSendEmail,
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
        throw new Error('Magento config set failed! ' + response.data);
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
      const event = await db
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
    getAllEvents: () => {
      return db.select().from(getTableName('emarsys_events_data'));
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
    setDefaultCustomerProperty: customerData => {
      defaultCustomer = { ...defaultCustomer, ...customerData };
      return defaultCustomer;
    },
    getSubscription: email => {
      return db
        .select()
        .from(getTableName('newsletter_subscriber'))
        .where({ subscriber_email: email })
        .first();
    },
    setDoubleOptin: stateOn => {
      if (stateOn) {
        return db
          .insert({
            scope: 'default',
            scope_id: 0,
            path: 'newsletter/subscription/confirm',
            value: 1
          })
          .into(getTableName('core_config_data'));
      } else {
        return db(getTableName('core_config_data'))
          .where({ path: 'newsletter/subscription/confirm' })
          .delete();
      }
    }
  });
};

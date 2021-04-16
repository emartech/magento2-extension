'use strict';

const chai = require('chai');
const chaiString = require('chai-string');
const chaiSubset = require('chai-subset');
const sinon = require('sinon');
const sinonChai = require('sinon-chai');
const url = require('url');
const Magento2ApiClient = require('@emartech/magento2-api');
const axios = require('axios');

const { cacheTablePrefix, getTableName } = require('./helpers/get-table-name');
const cartItem = require('./fixtures/cart-item');
const db = require('./helpers/db');
const DbCleaner = require('./db-cleaner');

chai.use(chaiString);
chai.use(chaiSubset);
chai.use(sinonChai);
global.expect = chai.expect;

const createCustomer = (magentoApi, db) => async (customer, password) => {
  await magentoApi.post({ path: '/index.php/rest/V1/customers', payload: { customer, password } });

  const { entity_id: entityId } = await db
    .select('entity_id')
    .from(getTableName('customer_entity'))
    .where({ email: customer.email })
    .first();

  return Object.assign({}, customer, { entityId, password });
};

const createProduct = (magentoApi) => (product) => {
  return magentoApi.post({ path: '/index.php/rest/V1/products', payload: { product } });
};

const setCurrencyConfig = async (db) => {
  await db(getTableName('core_config_data')).where({ path: 'currency/options/default' }).update({ value: 'UGX' });
};

const setDefaultStoreSettings = (magentoApi) => () => {
  return magentoApi.execute('config', 'set', {
    websiteId: 1,
    config: {
      storeSettings: [
        {
          storeId: 0,
          slug: 'testadminslug'
        },
        {
          storeId: 1,
          slug: 'testslug'
        }
      ]
    }
  });
};

const turnOffEverySetting = (magentoApi) => (websiteId) => {
  return magentoApi.execute('config', 'set', {
    websiteId,
    config: {
      collectCustomerEvents: 'disabled',
      collectSalesEvents: 'disabled',
      collectMarketingEvents: 'disabled',
      magentoSendEmail: 'disabled',
      injectSnippet: 'disabled',
      merchantId: '',
      webTrackingSnippetUrl: ''
    }
  });
};

const clearStoreSettings = (magentoApi) => () => {
  return magentoApi.execute('config', 'set', {
    websiteId: 1,
    config: {
      storeSettings: []
    }
  });
};

const triggerCustomEvent = (baseUrl) => ({ eventId, eventData, storeId }) => {
  const payload = {
    id: eventId,
    data: eventData
  };

  if (storeId) {
    payload.store_id = storeId;
  }

  return axios.post(`${baseUrl}trigger_event.php`, payload);
};

const cacheFlush = (baseUrl) => () => {
  return axios.get(`${baseUrl}cache-flush.php`);
};

const reindex = (baseUrl) => () => {
  return axios.get(`${baseUrl}reindex.php`);
};

before(async function () {
  await cacheTablePrefix();

  this.getTableName = getTableName;
  this.db = db;

  this.dbCleaner = DbCleaner.create(this.db);

  const { value: baseUrl } = await this.db
    .select('value')
    .from(this.getTableName('core_config_data'))
    .where({ path: 'web/unsecure/base_url' })
    .first();

  this.hostname = url.parse(baseUrl).host;
  this.magentoApi = new Magento2ApiClient({
    baseUrl: `http://${this.hostname}`,
    token: 'Almafa456',
    platform: 'magento2'
  });

  this.setDefaultStoreSettings = setDefaultStoreSettings(this.magentoApi);
  this.clearStoreSettings = clearStoreSettings(this.magentoApi);
  this.turnOffEverySetting = turnOffEverySetting(this.magentoApi);

  const magentoSystemInfo = await this.magentoApi.execute('systeminfo', 'get');
  this.magentoVersion = magentoSystemInfo.magento_version;
  this.magentoEdition = magentoSystemInfo.magento_edition;

  console.log('----------------------');
  console.log(`Hostname: ${this.hostname}`);
  console.log(`Table prefix: ${getTableName('')}`);
  console.log(`Magento version in mocha: ${this.magentoVersion} (${this.magentoEdition})`);
  console.log('----------------------');

  await this.setDefaultStoreSettings();

  await setCurrencyConfig(this.db);

  this.createCustomer = createCustomer(this.magentoApi, this.db);
  this.createProduct = createProduct(this.magentoApi);
  this.triggerCustomEvent = triggerCustomEvent(baseUrl);
  this.cacheFlush = cacheFlush(baseUrl);
  this.reindex = reindex(baseUrl);

  this.localCartItem = await cartItem.get(this.magentoApi);

  this.customer = await this.createCustomer(
    {
      group_id: 0,
      dob: '1977-11-12',
      email: 'default@yolo.net',
      firstname: 'Yolo',
      lastname: 'Default',
      store_id: 1,
      website_id: 1,
      disable_auto_group_change: 0,
      custom_attributes: [
        {
          attribute_code: 'emarsys_test_favorite_car',
          value: 'skoda'
        }
      ]
    },
    'Password1234'
  );
});

beforeEach(async function () {
  this.sandbox = sinon.createSandbox();
});

afterEach(async function () {
  this.sandbox.restore();

  await Promise.all([this.dbCleaner.resetEmarsysEventsData()]);
});

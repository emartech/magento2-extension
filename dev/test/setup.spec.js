'use strict';

const chai = require('chai');
const chaiString = require('chai-string');
const chaiSubset = require('chai-subset');
const sinon = require('sinon');
const sinonChai = require('sinon-chai');
const knex = require('knex');
const DbCleaner = require('./db-cleaner');
const url = require('url');
const Magento2ApiClient = require('@emartech/magento2-api');
const { productFactory } = require('./factories/products');
const cartItem = require('./fixtures/cart-item');

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

const createProduct = magentoApi => async product => {
  await magentoApi.post({ path: '/index.php/rest/V1/products', payload: { product } });
  return product;
};

const deleteProduct = magentoApi => async product => {
  await magentoApi.delete({ path: `/index.php/rest/V1/products/${product.sku}` });
  return product;
};

const createCategory = magentoApi => async category => {
  try {
    const response = await magentoApi.post({ path: '/index.php/rest/V1/categories', payload: { category } });
    return response.data;
  } catch (error) {
    throw error;
  }
};

const deleteCategory = magentoApi => async categoryId => {
  return await magentoApi.delete({ path: `/index.php/rest/V1/categories/${categoryId}` });
};

const getTableName = table => `${process.env.TABLE_PREFIX}${table}`;

const setCurrencyConfig = async db => {
  await db(getTableName('core_config_data'))
    .where({ path: 'currency/options/default' })
    .update({ value: 'UGX' });
  await db(getTableName('core_config_data'))
    .where({ path: 'currency/options/allow' })
    .update({ value: 'USD,UGX' });
  await db(getTableName('directory_currency_rate')).insert({
    currency_from: 'USD',
    currency_to: 'UGX',
    rate: '2'
  });
};

const setDefaultStoreSettings = magentoApi => async () => {
  return await magentoApi.execute('config', 'set', {
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

const clearStoreSettings = magentoApi => async () => {
  return await magentoApi.execute('config', 'set', {
    websiteId: 1,
    config: {
      storeSettings: []
    }
  });
};

const getMagentoSystemInfo = async magentoApi => {
  const result = await magentoApi.execute('systeminfo', 'get');
  return result;
};

before(async function() {
  console.log(`MAGENTO TABLE PREFIX: ${process.env.TABLE_PREFIX}`);

  this.getTableName = getTableName;

  this.db = knex({
    client: 'mysql',
    connection: {
      host: process.env.MYSQL_HOST,
      user: process.env.MYSQL_USER,
      password: process.env.MYSQL_PASSWORD,
      database: process.env.MYSQL_DATABASE
    }
  });

  this.dbCleaner = DbCleaner.create(this.db);

  const { token } = await this.db
    .select('token')
    .from(this.getTableName('integration'))
    .where({ name: 'Emarsys Integration' })
    .leftJoin(
      this.getTableName('oauth_token'),
      this.getTableName('integration.consumer_id'),
      this.getTableName('oauth_token.consumer_id')
    )
    .first();

  const { value: baseUrl } = await this.db
    .select('value')
    .from(this.getTableName('core_config_data'))
    .where({ path: 'web/unsecure/base_url' })
    .first();

  const hostname = url.parse(baseUrl).host;
  this.hostname = hostname;
  this.token = token;
  console.log('host', hostname);
  console.log('Token: ' + token);

  this.magentoApi = new Magento2ApiClient({
    baseUrl: `http://${this.hostname}`,
    token: this.token,
    platform: 'magento2'
  });
  this.setDefaultStoreSettings = setDefaultStoreSettings(this.magentoApi);
  this.clearStoreSettings = clearStoreSettings(this.magentoApi);
  const magentoSystemInfo = await getMagentoSystemInfo(this.magentoApi);
  this.magentoVersion = magentoSystemInfo.magento_version;
  this.magentoEdition = magentoSystemInfo.magento_edition;

  console.log('----------------------');
  console.log(`MAGENTO VERSION IN MOCHA: ${this.magentoVersion} (${this.magentoEdition})`);
  console.log('----------------------');

  await this.magentoApi.execute('config', 'setDefault', 1);
  await this.setDefaultStoreSettings();

  await setCurrencyConfig(this.db);

  if (!process.env.QUICK_TEST) {
    this.createCustomer = createCustomer(this.magentoApi, this.db);
    this.createProduct = createProduct(this.magentoApi);
    this.deleteProduct = deleteProduct(this.magentoApi);
    this.createCategory = createCategory(this.magentoApi);
    this.deleteCategory = deleteCategory(this.magentoApi);
    this.localCartItem = cartItem.get(this.magentoVersion, this.magentoEdition);

    try {
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
    } catch (e) {
      console.log(e.response);
    }

    const { parentIds, childIds } = await createCategories(this.createCategory);
    this.createdParentCategoryIds = parentIds;

    this.product = await this.createProduct(productFactory({}));
    this.storedProductsForProductSync = [];
    const productsForProductSync = [
      productFactory({
        sku: 'PRODUCT-SYNC-SKU',
        name: 'Product For Product Sync',
        custom_attributes: [
          {
            attribute_code: 'description',
            value: 'Default products description'
          },
          {
            attribute_code: 'category_ids',
            value: [parentIds[1].toString(), childIds[0].toString()]
          }
        ]
      })
    ];
    for (const productForProductSync of productsForProductSync) {
      const result = await this.createProduct(productForProductSync);
      this.storedProductsForProductSync.push(result);
    }
  }
});

const createCategories = async function(createCategory) {
  const parent = await createCategory({
    parent_id: 2,
    name: 'Parent category',
    is_active: true
  });
  const child = await createCategory({
    parent_id: parent.id,
    name: 'Child category',
    is_active: true
  });
  const simple = await createCategory({
    parent_id: 2,
    name: 'Simple category',
    is_active: true
  });

  return { parentIds: [parent.id, simple.id], childIds: [child.id] };
};

beforeEach(async function() {
  this.sinon = sinon;
  this.sandbox = sinon.createSandbox();
});

afterEach(async function() {
  this.sandbox.restore();
  await this.dbCleaner.resetEmarsysEventsData();
});

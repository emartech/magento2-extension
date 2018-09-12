'use strict';

const chai = require('chai');
const chaiString = require('chai-string');
const chaiSubset = require('chai-subset');
const sinon = require('sinon');
const sinonChai = require('sinon-chai');
const knex = require('knex');
const DbCleaner = require('./db-cleaner');
const Magento2ApiClient = require('@emartech/magento2-api');
const { productFactory } = require('./factories/products');

chai.use(chaiString);
chai.use(chaiSubset);
chai.use(sinonChai);
global.expect = chai.expect;

const createCustomer = (magentoApi, db) => async (customer, password) => {
  await magentoApi.post({ path: '/index.php/rest/V1/customers', payload: { customer, password } });

  const { entity_id: entityId } = await db
    .select('entity_id')
    .from('customer_entity')
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
    return await magentoApi.createCategory(category);
  } catch (error) {
    throw error;
  }
};

const deleteCategory = magentoApi => async categoryId => {
  return await magentoApi.deleteCategory(categoryId);
};

const setCurrencyConfig = async db => {
  await db('core_config_data')
    .where({ path: 'currency/options/default' })
    .update({ value: 'UGX' });
  await db('core_config_data')
    .where({ path: 'currency/options/allow' })
    .update({ value: 'USD,UGX' });
  await db('directory_currency_rate').insert({
    currency_from: 'USD',
    currency_to: 'UGX',
    rate: '2'
  });
};

const setDefaultStoreSettings = magentoApi => async () => {
  return await magentoApi.setConfig({
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
  return await magentoApi.setConfig({
    websiteId: 1,
    config: {
      storeSettings: []
    }
  });
};

before(async function() {
  this.timeout(30000);
  this.db = knex({
    client: 'mysql',
    connection: {
      host: process.env.MYSQL_HOST,
      user: process.env.MYSQL_USER,
      password: process.env.MYSQL_PASSWORD,
      database: process.env.MYSQL_DATABASE
    }
  });

  const result = await this.db
    .select('value')
    .from('core_config_data')
    .where({ path: 'emartech/emarsys/connecttoken' })
    .first();

  const { hostname, token } = JSON.parse(Buffer.from(result.value, 'base64'));
  this.hostname = hostname;
  this.token = token;
  console.log('host', hostname);
  console.log('Token: ' + token);

  this.magentoApi = new Magento2ApiClient({
    baseUrl: `http://${this.hostname}`,
    token: this.token
  });
  this.setDefaultStoreSettings = setDefaultStoreSettings(this.magentoApi);
  this.clearStoreSettings = clearStoreSettings(this.magentoApi);

  await this.magentoApi.setDefaultConfig(1);
  await this.setDefaultStoreSettings();

  await setCurrencyConfig(this.db);

  if (!process.env.QUICK_TEST) {
    this.createCustomer = createCustomer(this.magentoApi, this.db);
    this.createProduct = createProduct(this.magentoApi);
    this.deleteProduct = deleteProduct(this.magentoApi);
    this.createCategory = createCategory(this.magentoApi);
    this.deleteCategory = deleteCategory(this.magentoApi);

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
          disable_auto_group_change: 0
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
  await DbCleaner.create(this.db).resetEmarsysData();
});

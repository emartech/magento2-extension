'use strict';

const Magento2ApiClient = require('@emartech/magento2-api');

const defaults = {
  collectCustomerEvents: 'disabled',
  collectSalesEvents: 'disabled',
  injectSnippet: 'disabled',
  merchantId: null,
  webTrackingSnippetUrl: null
};

const scopeId = 8;

describe('Config endpoint', function() {
  afterEach(async function() {
    await this.db
      .delete()
      .from('core_config_data')
      .where('scope_id', scopeId);
  });

  describe('setDefaultConfig', function() {
    it('should create default config for website', async function() {
      const magentoApi = new Magento2ApiClient({
        baseUrl: 'http://web',
        token: this.token
      });

      await magentoApi.setDefaultConfig(scopeId);

      const config = await this.db
        .select()
        .from('core_config_data')
        .where('scope_id', scopeId)
        .andWhere('path', 'like', 'emartech/emarsys/config/%');

      for (const key in defaults) {
        const configItem = config.find(item => item.path === `emartech/emarsys/config/${key}`);
        expect(configItem.value).to.be.equal(defaults[key]);
      }
    });
  });

  describe('set', function() {
    it('should modify config values for website', async function() {
      const magentoApi = new Magento2ApiClient({
        baseUrl: 'http://web',
        token: this.token
      });

      const testConfig = {
        collectCustomerEvents: 'enabled',
        collectSalesEvents: 'enabled',
        injectSnippet: 'enabled',
        merchantId: '1234567',
        webTrackingSnippetUrl: 'https://path/to/snippet'
      };

      await magentoApi.execute('config', 'set', {
        websiteId: scopeId,
        config: testConfig
      });

      const config = await this.db
        .select()
        .from('core_config_data')
        .where('scope_id', scopeId)
        .andWhere('path', 'like', 'emartech/emarsys/config/%');

      for (const key in testConfig) {
        const configItem = config.find(item => item.path === `emartech/emarsys/config/${key}`);
        expect(configItem.value).to.be.equal(testConfig[key]);
      }
    });
  });
});

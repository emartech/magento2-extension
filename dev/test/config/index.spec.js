'use strict';

const defaults = {
  collectCustomerEvents: 'disabled',
  collectSalesEvents: 'disabled',
  collectMarketingEvents: 'disabled',
  injectSnippet: 'disabled',
  merchantId: null,
  webTrackingSnippetUrl: null
};

const dbKeys = {
  collectCustomerEvents: 'collect_customer_events',
  collectSalesEvents: 'collect_sales_events',
  collectMarketingEvents: 'collect_marketing_events',
  injectSnippet: 'inject_webextend_snippets',
  merchantId: 'merchant_id',
  webTrackingSnippetUrl: 'web_tracking_snippet_url'
};

const scopeId = 1;
describe('Config endpoint', function() {
  afterEach(async function() {
    this.magentoApi.setDefaultConfig(1);
  });

  describe('setDefaultConfig', function() {
    it('should create default config for website', async function() {
      await this.db
        .delete()
        .from('core_config_data')
        .where('path', 'like', 'emartech/emarsys/config/%');

      await this.magentoApi.setDefaultConfig(scopeId);

      const config = await this.db
        .select()
        .from('core_config_data')
        .where('scope_id', scopeId)
        .andWhere('path', 'like', 'emartech/emarsys/config/%');

      for (const key in defaults) {
        const configItem = config.find(item => {
          return item.path === `emartech/emarsys/config/${dbKeys[key]}`;
        });
        expect(configItem.value).to.be.equal(defaults[key]);
      }
    });
  });

  describe('set', function() {
    it('should modify config values for website', async function() {
      const testConfig = {
        collectCustomerEvents: 'enabled',
        collectSalesEvents: 'enabled',
        collectMarketingEvents: 'enabled',
        injectSnippet: 'enabled',
        merchantId: '1234567',
        webTrackingSnippetUrl: 'https://path/to/snippet'
      };

      await this.magentoApi.execute('config', 'set', {
        websiteId: scopeId,
        config: testConfig
      });

      const config = await this.db
        .select()
        .from('core_config_data')
        .where('scope_id', scopeId)
        .andWhere('path', 'like', 'emartech/emarsys/config/%');

      for (const key in testConfig) {
        const configItem = config.find(item => item.path === `emartech/emarsys/config/${dbKeys[key]}`);
        expect(configItem.value).to.be.equal(testConfig[key]);
      }
    });
  });
});

'use strict';

const defaults = {
  collectCustomerEvents: 'disabled',
  collectSalesEvents: 'disabled',
  collectMarketingEvents: 'disabled',
  injectSnippet: 'disabled',
  merchantId: null,
  webTrackingSnippetUrl: null
};

const fullConfig = {
  collectCustomerEvents: 'enabled',
  collectSalesEvents: 'enabled',
  collectMarketingEvents: 'enabled',
  injectSnippet: 'enabled',
  merchantId: '1234567',
  webTrackingSnippetUrl: 'https://path/to/snippet'
};

const dbKeys = {
  collectCustomerEvents: 'collect_customer_events',
  collectSalesEvents: 'collect_sales_events',
  collectMarketingEvents: 'collect_marketing_events',
  injectSnippet: 'inject_webextend_snippets',
  merchantId: 'merchant_id',
  webTrackingSnippetUrl: 'web_tracking_snippet_url'
};

const websiteId = 1;
describe('Config endpoint', function() {
  afterEach(async function() {
    await this.magentoApi.execute('config', 'setDefault', 1);
  });

  after(async function() {
    await this.setDefaultStoreSettings();
  });

  describe('setDefaultConfig', function() {
    it('should create default config for website', async function() {
      await this.magentoApi.execute('config', 'set', {
        websiteId,
        config: fullConfig
      });

      await this.db
        .delete()
        .from('core_config_data')
        .where('path', 'like', 'emartech/emarsys/config/%');

      await this.magentoApi.execute('config', 'setDefault', websiteId);

      const config = await this.db
        .select()
        .from('core_config_data')
        .where('scope_id', websiteId)
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
      await this.magentoApi.execute('config', 'set', {
        websiteId,
        config: fullConfig
      });

      const config = await this.db
        .select()
        .from('core_config_data')
        .where('scope_id', websiteId)
        .andWhere('path', 'like', 'emartech/emarsys/config/%');

      for (const key in fullConfig) {
        const configItem = config.find(item => item.path === `emartech/emarsys/config/${dbKeys[key]}`);
        expect(configItem.value).to.be.equal(fullConfig[key]);
      }
    });
  });
});

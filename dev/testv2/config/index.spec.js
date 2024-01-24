'use strict';

const fullConfig = {
  collectCustomerEvents: 'enabled',
  collectSalesEvents: 'enabled',
  collectMarketingEvents: 'enabled',
  magentoSendEmail: 'enabled',
  injectSnippet: 'enabled',
  merchantId: '1234567',
  webTrackingSnippetUrl: 'https://path/to/snippet'
};

const dbKeys = {
  collect_customer_events: 'collectCustomerEvents',
  collect_sales_events: 'collectSalesEvents',
  collect_marketing_events: 'collectMarketingEvents',
  inject_webextend_snippets: 'injectSnippet',
  merchant_id: 'merchantId',
  web_tracking_snippet_url: 'webTrackingSnippetUrl',
  magento_send_email: 'magentoSendEmail'
};

const websiteId = 1;
describe('Config endpoint', function() {
  before(async function() {
    await this.turnOffEverySetting(1);
  });

  afterEach(async function() {
    await this.turnOffEverySetting(1);
  });

  after(async function() {
    await this.setDefaultStoreSettings();
  });

  describe('set', function() {
    it('should modify config values for website', async function() {
      await this.magentoApi.execute('config', 'set', {
        websiteId,
        config: fullConfig
      });

      const configsInDB = await this.db
        .select()
        .from(this.getTableName('core_config_data'))
        .where('scope_id', websiteId)
        .andWhere('path', 'like', 'emartech/emarsys/config/%');

      const transformedConfigsFromDB = configsInDB.reduce((all, config) => ({
        ...all,
        [dbKeys[config.path.replace('emartech/emarsys/config/', '')]]: config.value
      }));

      expect(transformedConfigsFromDB).to.include(fullConfig);
    });
  });
});

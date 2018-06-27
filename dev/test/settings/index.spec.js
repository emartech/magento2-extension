'use strict';

const Magento2ApiClient = require('@emartech/magento2-api');

describe('Settings', function() {
  describe('defaults', function() {
    it('should have a collectCustomerEvents default config after install', async function() {
      const setting = await this.db
        .select()
        .from('emarsys_settings')
        .where({ setting: 'collectCustomerEvents ' })
        .first();

      expect(setting.value).to.eql('disabled');
    });

    it('should have a collectSalesEvents default config after install', async function() {
      const setting = await this.db
        .select()
        .from('emarsys_settings')
        .where({ setting: 'collectSalesEvents ' })
        .first();

      expect(setting.value).to.eql('disabled');
    });

    it('should have a collectProductEvents default config after install', async function() {
      const setting = await this.db
        .select()
        .from('emarsys_settings')
        .where({ setting: 'collectProductEvents ' })
        .first();

      expect(setting.value).to.eql('disabled');
    });
  });

  describe('api', function() {
    it('should modify a single setting', async function() {
      const magentoApi = new Magento2ApiClient({
        baseUrl: 'http://web',
        token: this.token
      });

      await magentoApi.setSettings({ collectCustomerEvents: 'enabled' });

      const setting = await this.db
        .select()
        .from('emarsys_settings')
        .where({ setting: 'collectCustomerEvents ' })
        .first();

      expect(setting.value).to.eql('enabled');
    });

    it('should store merchantId from settings', async function() {
      const merchantId = 'merchant123';
      const magentoApi = new Magento2ApiClient({
        baseUrl: 'http://web',
        token: this.token
      });

      await magentoApi.setSettings({ merchantId });

      const setting = await this.db
        .select()
        .from('emarsys_settings')
        .where({ setting: 'merchantId' })
        .first();

      expect(setting.value).to.eql(merchantId);
    });
  });
});

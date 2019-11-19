'use strict';

const axios = require('axios');
const cheerio = require('cheerio');

const getEmarsysSnippetContents = async path => {
  const response = await axios.get(`http://magento-test.local/index.php/${path}`);
  const $ = cheerio.load(response.data);
  return $('.emarsys-snippets')
    .html()
    .replace(/(?:\r\n|\r|\n)/g, '');
};

const alterProductVisibility = async (magentoApi, sku) => {
  await magentoApi.put({
    path: `/index.php/rest/V1/products/${sku}`,
    payload: {
      product: { visibility: 4 }
    }
  });
};

describe('Webextend scripts', function() {
  describe('enabled', function() {
    beforeEach(async function() {
      await this.magentoApi.execute('config', 'set', {
        websiteId: 1,
        config: {
          injectSnippet: 'enabled',
          merchantId: 'abc123',
          webTrackingSnippetUrl: 'http://yolo.hu/script'
        }
      });
    });

    it('should be in the HTML if injectsnippet is enabled', async function() {
      const emarsysSnippets = await getEmarsysSnippetContents('customer/account/login/');

      expect(emarsysSnippets.includes('<script src="http://yolo.hu/script"></script>')).to.be.true;

      expect(
        emarsysSnippets.includes(
          //eslint-disable-next-line
          `<script type="text/javascript">    var ScarabQueue = ScarabQueue || [];    (function(id) {      if (document.getElementById(id)) return;      var js = document.createElement('script'); js.id = id;      js.src = '\\/' + '\\/' + 'cdn.scarabresearch.com/js/abc123/scarab-v2.js';      var fs = document.getElementsByTagName('script')[0];      fs.parentNode.insertBefore(js, fs);    })('scarab-js-api');  </script>`
        )
      ).to.be.true;

      expect(
        emarsysSnippets.includes(
          //eslint-disable-next-line
          '<script>Emarsys.Magento2.track({"product":false,"category":false,"localizedCategory":false,"store":{"merchantId":"abc123"},"search":false,"exchangeRate":2,"slug":"testslug"});</script>'
        )
      ).to.be.true;
    });

    it('should include search term', async function() {
      const emarsysSnippets = await getEmarsysSnippetContents('catalogsearch/result/?q=magento+is+shit');
      expect(
        emarsysSnippets.includes(
          //eslint-disable-next-line
          '<script>Emarsys.Magento2.track({"product":false,"category":false,"localizedCategory":false,"store":{"merchantId":"abc123"},"search":{"term":"magento is shit"},"exchangeRate":2,"slug":"testslug"});</script>'
        )
      ).to.be.true;
    });

    it('should include category', async function() {
      const emarsysSnippets = await getEmarsysSnippetContents('men/tops-men.html');
      let categoryIds;
      if (
        (this.magentoVersion === '2.3.1' || this.magentoVersion === '2.1.9') &&
        this.magentoEdition === 'Enterprise'
      ) {
        categoryIds = ['12', '13'];
      } else {
        categoryIds = ['11', '12'];
      }

      expect(
        emarsysSnippets.includes(
          `<script>Emarsys.Magento2.track({"product":false,"category":{"names":["Men","Tops"],"ids":${JSON.stringify(
            categoryIds
          )}},"localizedCategory":{"names":["Men","Tops"],"ids":${JSON.stringify(
            categoryIds
          )}},"store":{"merchantId":"abc123"},"search":false,"exchangeRate":2,"slug":"testslug"});</script>`
        )
      ).to.be.true;
    });

    it('should include product', async function() {
      const emarsysSnippets = await getEmarsysSnippetContents('cassius-sparring-tank.html');
      const fullVersion = this.magentoVersion + this.magentoEdition;
      let productId;
      switch (fullVersion) {
        case '2.3.0Community':
          productId = 730;
          break;
        case '2.3.1Community':
          productId = 730;
          break;
        case '2.3.2Community':
          productId = 730;
          break;
        case '2.3.3Community':
          productId = 730;
          break;
        case '2.1.9Enterprise':
          productId = 731;
          break;
        case '2.3.1Enterprise':
          productId = 732;
          break;
        case '2.3.2Enterprise':
          productId = 730;
          break;
        case '2.3.3Enterprise':
          productId = 730;
          break;
        default:
          productId = 729;
      }

      expect(
        emarsysSnippets.includes(
          //eslint-disable-next-line
          `<script>Emarsys.Magento2.track({"product":{"sku":"MT12","id":"${productId}","isVisibleChild":false},"category":false,"localizedCategory":false,"store":{"merchantId":"abc123"},"search":false,"exchangeRate":2,"slug":"testslug"});</script>`
        )
      ).to.be.true;
    });

    it('should include if product is visible child', async function() {
      await alterProductVisibility(this.magentoApi, 'MT12-XS-Blue');
      const emarsysSnippets = await getEmarsysSnippetContents('cassius-sparring-tank-xs-blue.html');

      expect(emarsysSnippets.includes('"sku":"MT12-XS-Blue"')).to.be.true;
      expect(emarsysSnippets.includes('"isVisibleChild":true')).to.be.true;
    });

    describe('store is not enabled', function() {
      before(async function() {
        await this.clearStoreSettings();
      });

      after(async function() {
        await this.setDefaultStoreSettings();
      });

      it('should not be in the HTML', async function() {
        await this.magentoApi.execute('config', 'setDefault', 1);
        const emarsysSnippets = await getEmarsysSnippetContents('customer/account/login/');
        expect(emarsysSnippets).to.eql('');
      });
    });
  });

  describe('disabled', function() {
    it('should not be in the HTML if injectsnippet setting is disabled', async function() {
      await this.magentoApi.execute('config', 'setDefault', 1);
      const emarsysSnippets = await getEmarsysSnippetContents('customer/account/login/');
      expect(emarsysSnippets).to.eql('');
    });
  });
});

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

describe('Webextend scripts', function() {
  describe('enabled', function() {
    beforeEach(async function() {
      await this.magentoApi.setConfig({
        websiteId: 1,
        config: {
          injectSnippet: 'enabled',
          merchantId: '123',
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
          `<script type="text/javascript">    var ScarabQueue = ScarabQueue || [];    (function(id) {      if (document.getElementById(id)) return;      var js = document.createElement('script'); js.id = id;      js.src = '//cdn.scarabresearch.com/js/123/scarab-v2.js';      var fs = document.getElementsByTagName('script')[0];      fs.parentNode.insertBefore(js, fs);    })('scarab-js-api');  </script>`
        )
      ).to.be.true;

      expect(
        emarsysSnippets.includes(
          //eslint-disable-next-line
          '<script>Emarsys.Magento2.track({"product":false,"category":false,"store":{"merchantId":"123"},"search":false,"exchangeRate":2});</script>'
        )
      ).to.be.true;
    });

    it('should include search term', async function() {
      const emarsysSnippets = await getEmarsysSnippetContents('catalogsearch/result/?q=magento+is+shit');
      expect(
        emarsysSnippets.includes(
          //eslint-disable-next-line
          '<script>Emarsys.Magento2.track({"product":false,"category":false,"store":{"merchantId":"123"},"search":{"term":"magento is shit"},"exchangeRate":2});</script>'
        )
      ).to.be.true;
    });

    it('should include category', async function() {
      const emarsysSnippets = await getEmarsysSnippetContents('men/tops-men.html');
      expect(
        emarsysSnippets.includes(
          //eslint-disable-next-line
          '<script>Emarsys.Magento2.track({"product":false,"category":{"names":["Men","Tops"],"ids":["11","12"]},"store":{"merchantId":"123"},"search":false,"exchangeRate":2});</script>'
        )
      ).to.be.true;
    });

    it('should include product', async function() {
      const emarsysSnippets = await getEmarsysSnippetContents('cassius-sparring-tank.html');
      expect(
        emarsysSnippets.includes(
          //eslint-disable-next-line
          '<script>Emarsys.Magento2.track({"product":{"sku":"MT12","id":"729"},"category":false,"store":{"merchantId":"123"},"search":false,"exchangeRate":2});</script>'
        )
      ).to.be.true;
    });
  });

  describe('disabled', function() {
    it('should not be in the HTML if injectsnippet is disabled', async function() {
      await this.magentoApi.setDefaultConfig(1);
      const emarsysSnippets = await getEmarsysSnippetContents('customer/account/login/');
      expect(emarsysSnippets).to.eql('');
    });
  });
});

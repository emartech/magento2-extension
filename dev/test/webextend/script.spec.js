'use strict';

const Magento2ApiClient = require('@emartech/magento2-api');
const axios = require('axios');
const cheerio = require('cheerio');

let magentoApi;

describe('Webextend scripts', function() {
  before(function() {
    magentoApi = new Magento2ApiClient({
      baseUrl: 'http://web',
      token: this.token
    });
  });

  it('should be in the HTML if injectsnippet is enabled', async function() {
    await magentoApi.setSettings({
      injectSnippet: 'enabled',
      merchantId: '123',
      webTrackingSnippetUrl: 'http://yolo.hu/script'
    });

    const response = await axios.get('http://magento.local/index.php/customer/account/login/');
    const $ = cheerio.load(response.data);
    const emarsysSnippets = $('.emarsys-snippets').html().replace(/(?:\r\n|\r|\n)/g, '');

    expect(emarsysSnippets.includes('<script src="http://yolo.hu/script"></script>')).to.be.true;

    //eslint-disable-next-line
    expect(emarsysSnippets.includes(`<script type="text/javascript">    var ScarabQueue = ScarabQueue || [];    (function(id) {      if (document.getElementById(id)) return;      var js = document.createElement('script'); js.id = id;      js.src = '//cdn.scarabresearch.com/js/123/scarab-v2.js';      var fs = document.getElementsByTagName('script')[0];      fs.parentNode.insertBefore(js, fs);    })('scarab-js-api');  </script>`)).to.be.true;

    //eslint-disable-next-line
    expect(emarsysSnippets.includes('<script>Emarsys.Magento2.track({"product":false,"category":false,"store":{"merchantId":"123"},"search":false});</script>')).to.be.true;
  });

  it('should not be in the HTML if injectsnippet is disabled', async function() {
    const response = await axios.get('http://magento.local/index.php/customer/account/login/');
    const $ = cheerio.load(response.data);
    const emarsysSnippets = $('.emarsys-snippets').html().replace(/(?:\r\n|\r|\n)/g, '');

    expect(emarsysSnippets).to.eql('');
  });
});

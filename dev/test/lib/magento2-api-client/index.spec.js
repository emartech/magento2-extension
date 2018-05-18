'use strict';

const Magento2ApiClient = require('./index');

describe('magento2 api client', function() {
  let magento2ApiClient;

  before(function() {
    magento2ApiClient = Magento2ApiClient.create({ token: this.token });
  });

  it('should get basic api call', async function() {
    this.timeout(10000);
    const storeConfig = await magento2ApiClient.get({ url: '/index.php/rest/V1/store/storeConfigs' });
    expect(storeConfig.status).to.be.eql(200);
  });

  it('should post basic api call', async function() {
    this.timeout(10000);
    const productCreatePayload = {
      product: {
        sku: 'B201-SKU',
        name: 'B202',
        price: 30.0,
        status: 1,
        type_id: 'simple',
        attribute_set_id: 4,
        weight: 1
      }
    };
    const productCreateResponse = await magento2ApiClient.post({
      url: '/index.php/rest/V1/products',
      payload: productCreatePayload
    });
    expect(productCreateResponse.status).to.be.eql(200);
  });
});

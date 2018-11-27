'use strict';

describe('Connect', function() {
  it('should store hostname, token, magento_version', async function() {
    const result = await this.db
      .select('value')
      .from('core_config_data')
      .where({ path: 'emartech/emarsys/connecttoken' })
      .first();

    const { hostname, token, magento_version: magentoVersion } = JSON.parse(Buffer.from(result.value, 'base64'));
    expect('http://magento-test.local/'.includes(hostname)).to.be.true;
    expect(token).not.to.be.undefined;
    expect(magentoVersion).to.equal(2);
  });
});

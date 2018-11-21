'use strict';

describe('SystemInfo API', function() {
  it('should return system information', async function() {
    const expectedInfo = {
      magento_version: '2.2.6',
      php_version: '7.1.17',
      module_version: '1.1.2',
      magento_edition: 'Community'
    };

    const info = await this.magentoApi.execute('systeminfo', 'get');

    expect(info).to.eql(expectedInfo);
  });
});

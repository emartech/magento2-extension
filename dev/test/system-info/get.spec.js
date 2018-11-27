'use strict';

describe('SystemInfo API', function() {
  it('should return system information', async function() {

    const info = await this.magentoApi.execute('systeminfo', 'get');

    expect(info.magento_version).to.not.be.undefined;
    expect(info.php_version).to.not.be.undefined;
    expect(info.module_version).to.not.be.undefined;
    expect(info.magento_edition).to.not.be.undefined;
  });
});

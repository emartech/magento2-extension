'use strict';

describe('SystemInfo API', function() {
  it('should return system information', async function() {

    const info = await this.magentoApi.execute('systeminfo', 'get');

    expect(info.magento_version).to.not.be.undefined;
    expect(info.php_version).to.not.be.undefined;
    expect(info.module_version).to.not.be.undefined;
    expect(info.magento_edition).to.not.be.undefined;
  });

  it('should return customer DB website scope when configured so', async function() {
    const info = await this.magentoApi.execute('systeminfo', 'get');

    expect(info.is_website_scope).to.be.true;
  });

  it('should return customer DB website scope when configured so', async function() {
    await this.db(this.getTableName('core_config_data')).insert(
      { scope: 'default', path: 'customer/account_share/scope', value: 0 }
    );

    await this.cacheFlush();

    const info = await this.magentoApi.execute('systeminfo', 'get');

    expect(info.is_website_scope).to.be.false;

    await this.db(this.getTableName('core_config_data'))
      .where({ path: 'customer/account_share/scope' })
      .delete();

    await this.cacheFlush();
  });
});

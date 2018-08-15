'use strict';

describe('Connect', function() {
  it.skip('should store hostname, token', async function() {
    const result = await this.db
      .select('value')
      .from('core_config_data')
      .where({ path: 'emartech/emarsys/connecttoken' })
      .first();

    const { hostname, token } = JSON.parse(Buffer.from(result.value, 'base64'));
    expect('http://web/'.includes(hostname)).to.be.true;
    expect(token).not.to.be.undefined;
  });
});

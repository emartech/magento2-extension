'use strict';

describe('Connect', function() {
  it('should work', async function() {
    const result = await this.db.select().from('admin_user');
    expect(result).not.to.undefined;
  });
});

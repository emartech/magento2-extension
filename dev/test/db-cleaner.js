'use strict';

module.exports = class DbCleaner {
  static create(db) {
    return new DbCleaner(db);
  }

  constructor(db) {
    this._db = db;
  }

  async tearDown() {
    await this._db('emarsys_settings').update({
      value: 'disabled'
    });
    await this._db.raw('DELETE FROM customer_entity');
    await this._db.truncate('emarsys_events');
  }
};

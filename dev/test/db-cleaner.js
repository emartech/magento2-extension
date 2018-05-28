'use strict';

module.exports = class DbCleaner {
  static create(db) {
    return new DbCleaner(db);
  }

  constructor(db) {
    this._db = db;
  }

  async tearDown() {
    return this._db('emarsys_settings').update({
      value: 'disabled'
    });
  }
};

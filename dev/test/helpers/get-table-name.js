'use strict';

const db = require('./db');

let prefix;

const cacheTablePrefix = async () => {
  const rows = await db('information_schema.tables')
    .select('table_name')
    .where('table_name', 'like', '%core_config_data');

  const { table_name: tableName } = rows[0];

  prefix = tableName.split('core_config_data')[0];
};

const getTableName = tableName => {
  return `${prefix}${tableName}`;
};

module.exports = { cacheTablePrefix, getTableName };

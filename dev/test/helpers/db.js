'use strict';

const knex = require('knex');

module.exports = knex(
  process.env.CYPRESS_baseUrl
    ? {
      client: 'mysql',
      connection: {
        host: '127.0.0.1',
        port: 13306,
        user: 'magento',
        password: 'magento',
        database: 'magento_test'
      }
    }
    : {
      client: 'mysql',
      connection: {
        host: process.env.MYSQL_HOST,
        user: process.env.MYSQL_USER,
        password: process.env.MYSQL_PASSWORD,
        database: process.env.MYSQL_DATABASE
      }
    }
);

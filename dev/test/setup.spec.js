'use strict';

const chai = require('chai');
const chaiString = require('chai-string');
const chaiSubset = require('chai-subset');
const sinon = require('sinon');
const sinonChai = require('sinon-chai');
const knex = require('knex');

chai.use(chaiString);
chai.use(chaiSubset);
chai.use(sinonChai);
global.expect = chai.expect;

before(function() {
  this.db = knex({
    client: 'mysql',
    connection: {
      host: process.env.MYSQL_HOST,
      user: process.env.MYSQL_USER,
      password: process.env.MYSQL_PASSWORD,
      database: process.env.MYSQL_DATABASE
    }
  });
});

beforeEach(async function() {
  this.sinon = sinon;
  this.sandbox = sinon.sandbox.create();
});

afterEach(async function() {
  this.sandbox.restore();
});

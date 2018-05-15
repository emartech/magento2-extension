'use strict';

const chai = require('chai');
const chaiString = require('chai-string');
const chaiSubset = require('chai-subset');
const sinon = require('sinon');
const sinonChai = require('sinon-chai');

chai.use(chaiString);
chai.use(chaiSubset);
chai.use(sinonChai);
global.expect = chai.expect;

beforeEach(async function() {
  this.sinon = sinon;
  this.sandbox = sinon.sandbox.create();
});

afterEach(async function() {
  this.sandbox.restore();
});

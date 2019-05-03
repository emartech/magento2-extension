'use strict';

require('cypress-plugin-retries');
require('./commands');

afterEach(() => {
  cy.wait(4000);
  cy.task('clearEvents');
});

Cypress.on('uncaught:exception', (err, runnable) => { // eslint-disable-line no-unused-vars
  console.log('uncaught:exception', err.toString());
  return false;
});

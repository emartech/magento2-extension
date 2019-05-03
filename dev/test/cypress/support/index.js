'use strict';

require('cypress-plugin-retries');
require('./commands');

afterEach(() => {
  cy.task('clearEvents');
  cy.wait(4000);
});

Cypress.on('uncaught:exception', (err, runnable) => { // eslint-disable-line no-unused-vars
  console.log('uncaught:exception', err.toString());
  return false;
});

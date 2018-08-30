'use strict';

require('./commands');

afterEach(() => {
  cy.wait(2000);
  cy.task('clearEvents');
});

Cypress.on('uncaught:exception', (err, runnable) => { // eslint-disable-line no-unused-vars
  cy.task('log', err.toString());
  return false;
});

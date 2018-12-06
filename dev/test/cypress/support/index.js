'use strict';

require('./commands');

before(()=> {
  cy.task('setConfig', {
    websiteId: 1,
    config: {
      storeSettings: [
        {
          storeId: 0,
          slug: 'cypress-testadminslug'
        },
        {
          storeId: 1,
          slug: 'cypress-testslug'
        }
      ]
    }
  });
});

afterEach(() => {
  cy.wait(4000);
  cy.task('clearEvents');
});

Cypress.on('uncaught:exception', (err, runnable) => { // eslint-disable-line no-unused-vars
  cy.task('log', err.toString());
  return false;
});

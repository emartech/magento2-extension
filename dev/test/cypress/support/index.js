'use strict';

require('./commands');

before(() => {
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

beforeEach(() => {
  Cypress.cy.onUncaughtException = function() {
    console.log('UNCAUGHT_EXCEPTION', arguments);
    return false;
  };
});

afterEach(() => {
  cy.wait(4000);
  cy.task('clearEvents');
});

Cypress.on('fail', (error, runnable) => {
  console.log('FAILING_TEST', error, runnable);
  throw error;
});

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
  cy.task('clearEvents');
});

Cypress.on('uncaught:exception', (err, runnable) => { // eslint-disable-line no-unused-vars
  cy.task('log', err.toString());
  return false;
});

const erroredXHRRequest = test => {
  return test.commands.filter(command => {
    if (command.name === 'xhr') {
      return command.snapshots.some(snapshot => snapshot.name === 'error');
    }
  }).length;
};

const customerDataInStack = err => err.stack.indexOf('Magento/luma/en_US/Magento_Customer/js/customer-data.js') > -1;

const invalidFail = (err, test) => {
  const hasCustomDataJsInStack = customerDataInStack(err);
  const hasErroredXhrRequest = erroredXHRRequest(test);
  return hasCustomDataJsInStack && hasErroredXhrRequest;
};

Cypress.on('fail', (err, runnable) => {
  if (invalidFail(err, runnable)) {
    return true;
  }
  throw err;
});

'use strict';

const chaiSubset = require('chai-subset');
chai.use(chaiSubset);

Cypress.Commands.add('shouldCreateEvent', (type, expectedDataSubset) => {
  cy.task('getEventTypeFromDb', type).then((event) => {
    expect(event).to.not.null;
    expect(event.event_data).to.containSubset(expectedDataSubset);
  });
});

Cypress.Commands.add('shouldNotExistsEvents', () => {
  cy.task('getAllEvents').then((events) => {
    expect(events.length).to.be.empty;
  });
});

Cypress.Commands.add('loginWithCustomer', ({ customer }) => {
  cy.visit('/index.php/customer/account/login');
  cy.wait(2000);

  cy.get('input[name="login[username]"]').type(customer.email);
  cy.get('input[name="login[password]"]').type(customer.password);
  cy.get('button.login').click();

  cy.wait(3000);
  cy.get('.customer-name').should('be.visible');
});

Cypress.Commands.add('logout', () => {
  cy.visit('/');
  cy.get('.customer-name').click();

  cy.contains('Sign Out').click();
});

Cypress.Commands.add('shouldNotShowErrorMessage', (excludeErrorMessage) => {
  if (excludeErrorMessage) {
    return cy.get('[data-ui-id="message-error"]').invoke('text').should('contain', excludeErrorMessage);
  } else {
    return cy.get('[data-ui-id="message-error"]').should('not.be.visible');
  }
});

Cypress.Commands.add('clog', (logObject) => {
  cy.task('log', logObject);
});

Cypress.Commands.add('isSubscribed', (email, doubleOptin) => {
  const expectedStatus = doubleOptin ? 2 : 1;
  cy.task('getSubscription', email).then((subscription) => {
    expect(subscription.subscriber_status).to.be.equal(expectedStatus);
  });
});

Cypress.Commands.add('isNotSubscribed', (email) => {
  cy.task('getSubscription', email).then((subscription) => {
    expect(subscription.subscriber_status).to.not.equal(1);
    expect(subscription.subscriber_status).to.not.equal(2);
  });
});

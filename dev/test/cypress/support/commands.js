'use strict';

const chaiSubset = require('chai-subset');
chai.use(chaiSubset);

Cypress.Commands.add('shouldCreateEvent', (type, expectedDataSubset) => {
  cy.task('getEventTypeFromDb', type).then(event => {
    expect(event).to.not.null;
    expect(event.event_data).to.containSubset(expectedDataSubset);
  });
});

Cypress.Commands.add('shouldNotExistsEvents', () => {
  cy.task('getAllEvents').then(events => {
    expect(events.length).to.be.empty;
  });
});

Cypress.Commands.add('loginWithCustomer', ({ email, password }) => {
  cy.visit('/customer/account/login');

  cy.get('input[name="login[username]"]').type(email);
  cy.get('input[name="login[password]"]').type(password);
  cy.get('button.login').click();

  cy.get('.customer-name').should('be.visible');
});

Cypress.Commands.add('changeCredentials', (currentPassword, { email, password }) => {
  cy.get('.box-information > .box-actions > .edit > span').click();

  if (password) {
    cy.get('.page-wrapper #change-password').check();
    cy.get('.page-wrapper #password').type(password);
    cy.get('.page-wrapper #password-confirmation').type(password);
  }

  if (email) {
    cy.get('.page-wrapper #change-email').check();
    cy.get('.page-wrapper #email')
      .clear()
      .type(email);
  }

  cy.get('.page-wrapper input[name="current_password"]').type(currentPassword);

  cy.get('.page-wrapper .action.save.primary').click();
});

Cypress.Commands.add('logout', () => {
  cy.visit('/customer/account/logout/');
});

Cypress.Commands.add('shouldNotShowErrorMessage', excludeErrorMessage => {
  if (excludeErrorMessage) {
    return cy.get('[data-ui-id="message-error"]').should($errorBox => {
      const errorMessage = $errorBox.text();
      expect(errorMessage).to.include(excludeErrorMessage);
    });
  } else {
    return cy.get('[data-ui-id="message-error"]').should('not.be.visible');
  }
});

Cypress.Commands.add('isSubscribed', (email, doubleOptin) => {
  const expectedStatus = doubleOptin ? 2 : 1;
  cy.task('getSubscription', email).then(subscription => {
    expect(subscription.subscriber_status).to.be.equal(expectedStatus);
  });
});

Cypress.Commands.add('isNotSubscribed', email => {
  cy.task('getSubscription', email).then(subscription => {
    expect(subscription.subscriber_status).to.not.equal(1);
    expect(subscription.subscriber_status).to.not.equal(2);
  });
});

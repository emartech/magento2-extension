'use strict';

const chaiSubset = require('chai-subset');
chai.use(chaiSubset);

Cypress.Commands.add('shouldCreateEvent', (type, expectedDataSubset) => {
  cy.task('getEventTypeFromDb', type).then((event) => {
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

  cy.get('input[name="login[username]"]').type(customer.email);
  cy.get('input[name="login[password]"]').type(customer.password);
  cy.get('button.login').click();

  cy.wait(3000);
  cy.get('.customer-name').should('be.visible');
});

// ***********************************************
// This example commands.js shows you how to
// create various custom commands and overwrite
// existing commands.
//
// For more comprehensive examples of custom
// commands please read more here:
// https://on.cypress.io/custom-commands
// ***********************************************
//
//
// -- This is a parent command --
// Cypress.Commands.add("login", (email, password) => { ... })
//
//
// -- This is a child command --
// Cypress.Commands.add("drag", { prevSubject: 'element'}, (subject, options) => { ... })
//
//
// -- This is a dual command --
// Cypress.Commands.add("dismiss", { prevSubject: 'optional'}, (subject, options) => { ... })
//
//
// -- This is will overwrite an existing command --
// Cypress.Commands.overwrite("visit", (originalFn, url, options) => { ... })

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

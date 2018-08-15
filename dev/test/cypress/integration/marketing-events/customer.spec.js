'use strict';

describe('Marketing Events', function() {
  it('First test', function() {
    cy.visit('http://web/');

    cy.screenshot();

    cy.get('strong.logo')
      .should('be.visible');
  });
});

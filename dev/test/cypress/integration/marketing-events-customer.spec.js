'use strict';

describe('Marketing Events', function() {
  beforeEach(() => {
    cy.task('setConfig', { collectMarketingEvents: 'enabled' });
    cy.task('getDefaultCustomer').as('defaultCustomer');
  });

  it('should create customer_password_reset event', function() {
    const newPassword = 'newPassword2';

    cy.loginWithCustomer(this.defaultCustomer);
    cy.changeCredentials(this.defaultCustomer.password, { password: newPassword });

    cy.shouldCreateEvent('customer_password_reset', {
      new_customer_email: this.defaultCustomer.email
    });
    cy.shouldNotShowErrorMessage();

    cy.task('setDefaultCustomerProperty', { password: newPassword });
    cy.task('clearEvents');

    cy.logout();
  });

  it('should create customer_email_changed event', function() {
    const newEmail = 'cypress3@default.com';

    cy.loginWithCustomer(this.defaultCustomer);
    cy.changeCredentials(this.defaultCustomer.password, { email: newEmail });

    cy.shouldCreateEvent('customer_email_changed', {
      new_customer_email: newEmail
    });
    cy.shouldNotShowErrorMessage();

    cy.task('setDefaultCustomerProperty', { email: newEmail });
    cy.task('clearEvents');

    cy.logout();
  });

  it('should create customer_email_and_password_changed event', function() {
    const newEmail = 'cypress4@default.com';
    const newPassword = 'newPassword3';

    cy.loginWithCustomer(this.defaultCustomer);
    cy.changeCredentials(this.defaultCustomer.password, { password: newPassword, email: newEmail });

    cy.shouldCreateEvent('customer_email_and_password_changed', {
      new_customer_email: newEmail
    });
    cy.shouldNotShowErrorMessage();

    cy.task('setDefaultCustomerProperty', { email: newEmail, password: newPassword });
    cy.task('clearEvents');

    cy.logout();
  });
});

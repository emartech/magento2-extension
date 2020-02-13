'use strict';

describe('Marketing Events', function() {
  const changeCredentialsAfterLogin = (customer, { password, email }) => {
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

    cy.get('.page-wrapper input[name="current_password"]').type(customer.password);

    cy.get('.page-wrapper .action.save.primary').click();
  };

  beforeEach(() => {
    cy.task('setConfig', { collectMarketingEvents: 'enabled' });
    cy.task('getDefaultCustomer').as('defaultCustomer');
  });

  it('should create customer_password_reset event', function() {
    const newPassword = 'newPassword2';

    cy.loginWithCustomer({ customer: this.defaultCustomer });
    changeCredentialsAfterLogin(this.defaultCustomer, { password: newPassword });

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

    cy.loginWithCustomer({ customer: this.defaultCustomer });
    changeCredentialsAfterLogin(this.defaultCustomer, { email: newEmail });

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

    cy.loginWithCustomer({ customer: this.defaultCustomer });
    changeCredentialsAfterLogin(this.defaultCustomer, { password: newPassword, email: newEmail });

    cy.shouldCreateEvent('customer_email_and_password_changed', {
      new_customer_email: newEmail
    });
    cy.shouldNotShowErrorMessage();

    cy.task('setDefaultCustomerProperty', { email: newEmail, password: newPassword });
    cy.task('clearEvents');

    cy.logout();
  });
});

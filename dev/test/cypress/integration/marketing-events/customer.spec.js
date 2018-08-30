'use strict';

describe('Marketing Events', function() {
  const changeCredentials = (customer, { password, email }) => {
    cy.visit('/index.php/customer/account/edit/');

    if (password) {
      cy.get('.page-wrapper #change-password').check();
      cy.get('.page-wrapper #password').type(password);
      cy.get('.page-wrapper #password-confirmation').type(password);
    }
    if (email) {
      cy.get('.page-wrapper #change-email').check();
      cy.get('.page-wrapper #email').clear().type(email);
    }
    cy.get('.page-wrapper input[name="current_password"]').type(customer.password);

    cy.get('.page-wrapper .action.save.primary').click();
  };

  beforeEach(() => {
    cy.task('getDefaultCustomer').as('defaultCustomer');
  });

  context('with settings off', function() {
    before(() => {
      cy.task('setConfig', { websiteId: 1, config: { collectMarketingEvents: 'disabled' } });
    });

    it('should not create newsletter_send_confirmation_success_email event', function() {
      const guestEmail = 'guest.email2@guest.com';
      cy.visit('/');

      cy.get('#newsletter').type(guestEmail);
      cy.get('.action.subscribe.primary[type="submit"]').click();

      cy.shouldNotExistsEvents();
      cy.wait(1000);
      cy.shouldNotShowErrorMessage('Something went wrong with the subscription.');
      cy.isSubscribed(guestEmail);
    });

    it('should not create customer_password_reset event', function() {
      const newPassword = 'newPassword1';

      cy.loginWithCustomer({ customer: this.defaultCustomer });
      changeCredentials(this.defaultCustomer, { password: newPassword });

      cy.shouldNotExistsEvents();
      cy.wait(1000);
      cy.shouldNotShowErrorMessage('Unable to send mail.');

      cy.task('setDefaultCustomerProperty', { password: newPassword });
    });

    it('should not create customer_email_changed event', function() {
      const newEmail = 'cypress2@default.com';

      cy.loginWithCustomer({ customer: this.defaultCustomer });
      changeCredentials(this.defaultCustomer, { email: newEmail });

      cy.shouldNotExistsEvents();
      cy.wait(1000);
      cy.shouldNotShowErrorMessage('Unable to send mail.');

      cy.task('setDefaultCustomerProperty', { email: newEmail });
    });

    it('should create customer_email_and_password_changed event', function() {
      const newEmail = 'cypress5@default.com';
      const newPassword = 'newPassword4';

      cy.loginWithCustomer({ customer: this.defaultCustomer });
      changeCredentials(this.defaultCustomer, { password: newPassword, email: newEmail });

      cy.shouldNotExistsEvents();
      cy.wait(1000);
      cy.shouldNotShowErrorMessage('Unable to send mail.');

      cy.task('setDefaultCustomerProperty', { email: newEmail, password: newPassword });
    });
  });

  context('with settings on', function() {
    before(() => {
      cy.task('setConfig', { websiteId: 1, config: { collectMarketingEvents: 'enabled' } });
    });

    it('should create newsletter_send_confirmation_success_email event', function() {
      const guestEmail = 'guest.email@guest.com';
      cy.visit('/');

      cy.get('#newsletter').type(guestEmail);
      cy.get('.action.subscribe.primary[type="submit"]').click();

      cy.wait(1000);
      cy.shouldCreateEvent('newsletter_send_confirmation_success_email', {
        confirmation_link: { subscriber_email: guestEmail }
      });
      cy.shouldNotShowErrorMessage();

      cy.isSubscribed(guestEmail);
    });

    it('should create customer_password_reset event', function() {
      const newPassword = 'newPassword2';

      cy.loginWithCustomer({ customer: this.defaultCustomer });
      changeCredentials(this.defaultCustomer, { password: newPassword });

      cy.shouldCreateEvent('customer_password_reset', {
        new_customer_email: this.defaultCustomer.email
      });
      cy.shouldNotShowErrorMessage();

      cy.task('setDefaultCustomerProperty', { password: newPassword });
    });

    it('should create customer_email_changed event', function() {
      const newEmail = 'cypress3@default.com';

      cy.loginWithCustomer({ customer: this.defaultCustomer });
      changeCredentials(this.defaultCustomer, { email: newEmail });

      cy.shouldCreateEvent('customer_email_changed', {
        new_customer_email: newEmail
      });
      cy.shouldNotShowErrorMessage();

      cy.task('setDefaultCustomerProperty', { email: newEmail });
    });

    it('should create customer_email_and_password_changed event', function() {
      const newEmail = 'cypress4@default.com';
      const newPassword = 'newPassword3';

      cy.loginWithCustomer({ customer: this.defaultCustomer });
      changeCredentials(this.defaultCustomer, { password: newPassword, email: newEmail });

      cy.shouldCreateEvent('customer_email_and_password_changed', {
        new_customer_email: newEmail
      });
      cy.shouldNotShowErrorMessage();

      cy.task('setDefaultCustomerProperty', { email: newEmail, password: newPassword });
    });
  });
});

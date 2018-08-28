'use strict';

describe('Marketing Events', function() {
  const changeCredentials = (customer, { password, email }) => {
    cy.visit('/index.php/customer/account/edit/');

    if (password) {
      cy.get('#change-password').check();
      cy.get('#password').type(password);
      cy.get('#password-confirmation').type(password);
    }
    if (email) {
      cy.get('#change-email').check();
      cy.get('#email').type(customer.email);

    }
    cy.get('input[name="current_password"]').type(customer.password);

    cy.get('.action.save.primary').click();
  };

  beforeEach(() => {
    cy.task('getDefaultCustomer').as('defaultCustomer');
  });

  context('with settings off', function() {
    before(() => {
      cy.task('setConfig', { websiteId: 1, config: { collectMarketingEvents: 'disabled' } });
    });

    it.skip('should not create newsletter_send_confirmation_success_email event', function() {
      const guestEmail = 'guest.email2@guest.com';
      cy.visit('/');

      cy.get('#newsletter').type(guestEmail);
      cy.get('.action.subscribe.primary[type="submit"]').click();

      cy.shouldNotExistsEvents();
      cy.wait(1000);
      cy.shouldNotShowErrorMessage();
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
    });

    it('should create customer_password_reset event', function() {
      cy.clog(this.defaultCustomer.password);
      const newPassword = 'newPassword2';

      cy.loginWithCustomer({ customer: this.defaultCustomer });
      changeCredentials(this.defaultCustomer, { password: newPassword });

      cy.shouldCreateEvent('customer_password_reset', {
        new_customer_email: this.defaultCustomer.email
      });

      cy.task('setDefaultCustomerProperty', { password: newPassword });
    });
  });
});

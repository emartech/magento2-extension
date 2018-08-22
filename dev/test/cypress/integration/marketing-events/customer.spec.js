'use strict';

describe('Marketing Events', function() {
  before(() => {
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
    });
  });

  context('with settings on', function() {
    before(() => {
      cy.task('setConfig', { websiteId: 1, config: { collectMarketingEvents: 'enabled' } });
      cy.wait(3000);
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
      cy.loginWithCustomer({ customer: this.defaultCustomer });

      cy.visit('/index.php/customer/account/edit/changepass/1/');

      const newPassword = 'newPassword1';

      cy.get('input[name="current_password"]').type(this.defaultCustomer.password);
      cy.get('#password').type(newPassword);
      cy.get('#password-confirmation').type(newPassword);

      cy.get('.action.save.primary').click();

      cy.shouldCreateEvent('customer_password_reset', {
        new_customer_email: this.defaultCustomer.email
      });
    });
  });
});

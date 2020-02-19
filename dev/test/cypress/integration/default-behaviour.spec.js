'use strict';

describe('Default behaviour with everything turned off', function() {
  before(() => {
    cy.task('setConfig', {});
  });

  beforeEach(() => {
    cy.task('getDefaultCustomer').as('defaultCustomer');
  });

  context('MarketingEvents - Customer', function() {
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

    it('should not create customer_password_reset event', function() {
      const newPassword = 'newPassword1';

      cy.loginWithCustomer({ customer: this.defaultCustomer });
      changeCredentialsAfterLogin(this.defaultCustomer, { password: newPassword });

      cy.shouldNotExistsEvents();
      cy.shouldNotShowErrorMessage('Unable to send mail');

      cy.task('setDefaultCustomerProperty', { password: newPassword });
      cy.task('clearEvents');

      cy.logout();
    });

    it('should not create customer_email_changed event', function() {
      const newEmail = 'cypress2@default.com';

      cy.loginWithCustomer({ customer: this.defaultCustomer });
      changeCredentialsAfterLogin(this.defaultCustomer, { email: newEmail });

      cy.shouldNotExistsEvents();
      // cy.shouldNotShowErrorMessage('Unable to send mail');

      cy.task('setDefaultCustomerProperty', { email: newEmail });
      cy.task('clearEvents');

      cy.logout();
    });

    it('should not create customer_email_and_password_changed event', function() {
      const newEmail = 'cypress5@default.com';
      const newPassword = 'newPassword4';

      cy.loginWithCustomer({ customer: this.defaultCustomer });
      changeCredentialsAfterLogin(this.defaultCustomer, { password: newPassword, email: newEmail });

      cy.shouldNotExistsEvents();
      cy.shouldNotShowErrorMessage('Unable to send mail');

      cy.task('setDefaultCustomerProperty', { email: newEmail, password: newPassword });
      cy.task('clearEvents');

      cy.logout();
    });
  });

  context('MarketingEvents - Subscription', function() {
    before(() => {
      cy.task('disableEmail');
      cy.task('flushMagentoCache');
    });

    after(() => {
      cy.task('enableEmail');
      cy.task('flushMagentoCache');
    });

    const unsubscribe = email => {
      cy.task('getSubscription', email).then(subscription => {
        cy.visit(`/newsletter/subscriber/unsubscribe?id=${subscription.subscriber_id}\
          &code=${subscription.subscriber_confirm_code}`);
      });
    };

    const subscribe = email => {
      cy.visit('/');
      cy.get('#newsletter').type(email);
      cy.get('.action.subscribe.primary[type="submit"]').click();
    };

    context('guest with double optin off', function() {
      it('should not create subscription events', function() {
        const guestEmail = 'no-event.doptin-off@guest-cypress.com';
        subscribe(guestEmail);

        cy.shouldNotExistsEvents();
        cy.shouldNotShowErrorMessage();
        cy.isSubscribed(guestEmail);

        unsubscribe(guestEmail);

        cy.shouldNotExistsEvents();
        cy.isNotSubscribed(guestEmail);
        cy.task('clearEvents');
      });
    });

    context('guest with double optin on', function() {
      before(() => {
        cy.task('setDoubleOptin', true);
        cy.task('flushMagentoCache');
      });

      after(() => {
        cy.task('setDoubleOptin', false);
      });

      it('should not create subscription events', function() {
        const guestEmail = 'no-event.doptin-on@guest-cypress.com';
        subscribe(guestEmail);

        cy.shouldNotExistsEvents();
        cy.shouldNotShowErrorMessage();
        cy.isSubscribed(guestEmail, true);

        unsubscribe(guestEmail);

        cy.shouldNotExistsEvents();
        cy.isNotSubscribed(guestEmail);
        cy.task('clearEvents');
      });
    });
  });
});

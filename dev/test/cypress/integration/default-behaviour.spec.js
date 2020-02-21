'use strict';

describe('Default behaviour with everything turned off', function() {
  before(() => {
    cy.task('setConfig', {});
  });

  beforeEach(() => {
    cy.task('getDefaultCustomer').as('defaultCustomer');
  });

  context('MarketingEvents - Customer', function() {
    afterEach(() => {
      cy.task('clearEvents');
      cy.logout();
    });

    it('should not create customer_password_reset event', function() {
      const newPassword = 'newPassword1';

      cy.loginWithCustomer(this.defaultCustomer);
      cy.changeCredentials(this.defaultCustomer.password, { password: newPassword });

      cy.shouldNotShowErrorMessage('Unable to send mail');
      cy.shouldNotExistsEvents();

      cy.task('setDefaultCustomerProperty', { password: newPassword });
    });

    it('should not create customer_email_changed event', function() {
      const newEmail = 'cypress2@default.com';

      cy.loginWithCustomer(this.defaultCustomer);
      cy.changeCredentials(this.defaultCustomer.password, { email: newEmail });

      // cy.shouldNotShowErrorMessage('Unable to send mail');
      cy.shouldNotExistsEvents();

      cy.task('setDefaultCustomerProperty', { email: newEmail });
    });

    it('should not create customer_email_and_password_changed event', function() {
      const newEmail = 'cypress5@default.com';
      const newPassword = 'newPassword4';

      cy.loginWithCustomer(this.defaultCustomer);
      cy.changeCredentials(this.defaultCustomer.password, { password: newPassword, email: newEmail });

      cy.shouldNotShowErrorMessage('Unable to send mail');
      cy.shouldNotExistsEvents();

      cy.task('setDefaultCustomerProperty', { email: newEmail, password: newPassword });
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

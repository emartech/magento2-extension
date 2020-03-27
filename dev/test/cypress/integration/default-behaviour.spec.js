'use strict';

describe('Default behaviour with everything turned off', function() {
  before(() => {
    cy.task('setConfig', {});
  });

  beforeEach(() => {
    cy.task('getDefaultCustomer').as('defaultCustomer');
    cy.task('clearMails');
  });

  after(() => {
    cy.task('clearMails');
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

      cy.shouldNotShowErrorMessage();
      cy.shouldNotExistsEvents();

      cy.task('setDefaultCustomerProperty', { password: newPassword });

      cy.task('getSentAddresses').then(emailAddresses => {
        expect(emailAddresses).to.be.eql([this.defaultCustomer.email]);
      });
    });

    it('should not create customer_email_changed event', function() {
      const oldEmail = this.defaultCustomer.email;
      const newEmail = 'cypress2@default.com';

      cy.loginWithCustomer(this.defaultCustomer);
      cy.changeCredentials(this.defaultCustomer.password, { email: newEmail });
      cy.task('setDefaultCustomerProperty', { email: newEmail });

      cy.shouldNotShowErrorMessage();
      cy.shouldNotExistsEvents();

      cy.task('getSentAddresses').then(emailAddresses => {
        expect(emailAddresses).to.be.eql([newEmail, oldEmail]);
      });
    });

    it('should not create customer_email_and_password_changed event', function() {
      const newEmail = 'cypress5@default.com';
      const newPassword = 'newPassword4';

      cy.loginWithCustomer(this.defaultCustomer);
      cy.changeCredentials(this.defaultCustomer.password, { password: newPassword, email: newEmail });

      cy.shouldNotShowErrorMessage();
      cy.shouldNotExistsEvents();

      cy.task('setDefaultCustomerProperty', { email: newEmail, password: newPassword });

      cy.task('getSentAddresses').then(emailAddresses => {
        expect(emailAddresses).to.be.eql([this.defaultCustomer.email]);
      });
    });
  });

  context('MarketingEvents - Subscription', function() {
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

        cy.task('getSentAddresses').then(emailAddresses => {
          expect(emailAddresses).to.be.eql([guestEmail]);
        });
        cy.task('clearMails');

        unsubscribe(guestEmail);

        cy.shouldNotExistsEvents();
        cy.isNotSubscribed(guestEmail);
        cy.task('clearEvents');

        cy.task('getSentAddresses').then(emailAddresses => {
          expect(emailAddresses).to.be.eql([guestEmail]);
        });
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

        cy.task('getSentAddresses').then(emailAddresses => {
          expect(emailAddresses).to.be.eql([guestEmail]);
        });
        cy.task('clearMails');

        unsubscribe(guestEmail);

        cy.shouldNotExistsEvents();
        cy.isNotSubscribed(guestEmail);
        cy.task('clearEvents');

        cy.task('getSentAddresses').then(emailAddresses => {
          expect(emailAddresses).to.be.eql([guestEmail]);
        });
      });
    });
  });
});

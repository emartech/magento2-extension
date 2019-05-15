'use strict';

describe('Default behaviour with everything turned off', function() {
  before(() => {
    cy.task('setConfig', {});
    cy.wait(1000);
  });

  beforeEach(() => {
    cy.task('getDefaultCustomer').as('defaultCustomer');
  });

  context('Web extend', function() {
    const merchantId = 'merchantId123';
    const webTrackingSnippetUrl = Cypress.env('snippetUrl');
    const predictUrl = `http://cdn.scarabresearch.com/js/${merchantId}/scarab-v2.js`;

    const expectWebExtendFilesNotToBeIncluded = () => {
      cy.on('window:load', win => {
        const scripts = win.document.getElementsByTagName('script');
        if (scripts.length) {
          let jsFilesToBeIncluded = [predictUrl, webTrackingSnippetUrl];
          for (let i = 0; i < scripts.length; i++) {
            if (jsFilesToBeIncluded.includes(scripts[i].src)) {
              jsFilesToBeIncluded = jsFilesToBeIncluded.filter(e => e !== scripts[i].src);
            }
          }
          expect(jsFilesToBeIncluded.length).to.be.equal(2);
        }
      });
    };

    it('should include proper web tracking data', function() {
      expectWebExtendFilesNotToBeIncluded();

      cy.visit('/');
      cy.wait(2000);
      cy.task('clearEvents');
    });
  });

  context('MarketingEvents - Customer', function() {
    const changeCredentialsAfterLogin = (customer, { password, email }) => {
      cy.get('.box-information > .box-actions > .edit > span').click();
      cy.wait(2000);

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
      cy.wait(1000);
      cy.shouldNotShowErrorMessage('Unable to send mail');

      cy.task('setDefaultCustomerProperty', { password: newPassword });
      cy.task('clearEvents');
    });

    it('should not create customer_email_changed event', function() {
      const newEmail = 'cypress2@default.com';

      cy.loginWithCustomer({ customer: this.defaultCustomer });
      changeCredentialsAfterLogin(this.defaultCustomer, { email: newEmail });

      cy.shouldNotExistsEvents();
      cy.wait(1000);
      cy.shouldNotShowErrorMessage('Unable to send mail');

      cy.task('setDefaultCustomerProperty', { email: newEmail });
      cy.task('clearEvents');
    });

    it('should create customer_email_and_password_changed event', function() {
      const newEmail = 'cypress5@default.com';
      const newPassword = 'newPassword4';

      cy.loginWithCustomer({ customer: this.defaultCustomer });
      changeCredentialsAfterLogin(this.defaultCustomer, { password: newPassword, email: newEmail });

      cy.shouldNotExistsEvents();
      cy.wait(1000);
      cy.shouldNotShowErrorMessage('Unable to send mail');

      cy.task('setDefaultCustomerProperty', { email: newEmail, password: newPassword });
      cy.task('clearEvents');
    });
  });

  context('MarketingEvents - Subscription', function() {
    const unsubscribe = email => {
      cy.task('getSubscription', email).then(subscription => {
        cy.visit(`/newsletter/subscriber/unsubscribe?id=${subscription.subscriber_id}\
          &code=${subscription.subscriber_confirm_code}`);
        cy.wait(1000);
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
        cy.wait(1000);
        cy.shouldNotShowErrorMessage('Something went wrong with the subscription.');
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
        cy.wait(1000);
        cy.task('flushMagentoCache');
        cy.wait(1000);
      });

      after(() => {
        cy.task('setDoubleOptin', false);
      });

      it('should not create subscription events', function() {
        const guestEmail = 'no-event.doptin-on@guest-cypress.com';
        subscribe(guestEmail);

        cy.shouldNotExistsEvents();
        cy.wait(1000);
        cy.shouldNotShowErrorMessage('Something went wrong with the subscription.');
        cy.isSubscribed(guestEmail, true);

        unsubscribe(guestEmail);

        cy.shouldNotExistsEvents();
        cy.isNotSubscribed(guestEmail);
        cy.task('clearEvents');
      });
    });
  });
});

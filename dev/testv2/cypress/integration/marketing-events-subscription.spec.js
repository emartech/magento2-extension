'use strict';

describe('Marketing Events', function() {
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

  beforeEach(() => {
    cy.task('clearMails');
    cy.task('clearEvents');
  });

  context('magentoSendEmails config is disabled', function() {
    before(() => {
      cy.task('setConfig', {
        collectMarketingEvents: 'enabled',
        magentoSendEmail: 'disabled'
      });
    });

    context('guest with double optin off', function() {
      it('should create subscription events', function() {
        const guestEmail = 'event.doptin-off.sub@guest-cypress.com';
        subscribe(guestEmail);

        cy.shouldCreateEvent('newsletter_send_confirmation_success_email', {
          subscriber: { subscriber_email: guestEmail }
        });
        cy.shouldNotShowErrorMessage();
        cy.isSubscribed(guestEmail);

        cy.task('getSentAddresses').then(emailAddresses => {
          expect(emailAddresses).to.be.empty;
        });

        unsubscribe(guestEmail);

        cy.shouldCreateEvent('newsletter_send_unsubscription_email', {
          subscriber: { subscriber_email: guestEmail }
        });
        cy.shouldNotShowErrorMessage();
        cy.isNotSubscribed(guestEmail);

        cy.task('getSentAddresses').then(emailAddresses => {
          expect(emailAddresses).to.be.empty;
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

      it('should create newsletter_send_confirmation_request_email event', function() {
        const guestEmail = 'event.doptin-on.sub@guest-cypress.com';
        subscribe(guestEmail);

        cy.shouldCreateEvent('newsletter_send_confirmation_request_email', {
          subscriber: { subscriber_email: guestEmail }
        });
        cy.shouldNotShowErrorMessage();
        cy.isSubscribed(guestEmail, true);

        cy.task('getSentAddresses').then(emailAddresses => {
          expect(emailAddresses).to.be.empty;
        });

        unsubscribe(guestEmail);

        cy.shouldCreateEvent('newsletter_send_unsubscription_email', {
          subscriber: { subscriber_email: guestEmail }
        });
        cy.shouldNotShowErrorMessage();
        cy.isNotSubscribed(guestEmail);

        cy.task('getSentAddresses').then(emailAddresses => {
          expect(emailAddresses).to.be.empty;
        });
      });
    });
  });

  context('magentoSendEmails config is enabled', function() {
    before(() => {
      cy.task('setConfig', {
        collectMarketingEvents: 'enabled',
        magentoSendEmail: 'enabled'
      });
    });

    context('guest with double optin off', function() {
      it('should create subscription events', function() {
        const guestEmail = 'event.doptin-off.sub@guest-cypress.com';
        subscribe(guestEmail);

        cy.shouldCreateEvent('newsletter_send_confirmation_success_email', {
          subscriber: { subscriber_email: guestEmail }
        });
        cy.shouldNotShowErrorMessage();
        cy.isSubscribed(guestEmail);

        cy.task('getSentAddresses').then(emailAddresses => {
          expect(emailAddresses).to.be.eql([guestEmail]);
        });
        cy.task('clearMails');

        unsubscribe(guestEmail);

        cy.shouldCreateEvent('newsletter_send_unsubscription_email', {
          subscriber: { subscriber_email: guestEmail }
        });
        cy.shouldNotShowErrorMessage();
        cy.isNotSubscribed(guestEmail);

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

      it('should create newsletter_send_confirmation_request_email event', function() {
        const guestEmail = 'event.doptin-on.sub@guest-cypress.com';
        subscribe(guestEmail);

        cy.shouldCreateEvent('newsletter_send_confirmation_request_email', {
          subscriber: { subscriber_email: guestEmail }
        });
        cy.shouldNotShowErrorMessage();
        cy.isSubscribed(guestEmail, true);

        cy.task('getSentAddresses').then(emailAddresses => {
          expect(emailAddresses).to.be.eql([guestEmail]);
        });
        cy.task('clearMails');

        unsubscribe(guestEmail);

        cy.shouldCreateEvent('newsletter_send_unsubscription_email', {
          subscriber: { subscriber_email: guestEmail }
        });
        cy.shouldNotShowErrorMessage();
        cy.isNotSubscribed(guestEmail);

        cy.task('getSentAddresses').then(emailAddresses => {
          expect(emailAddresses).to.be.eql([guestEmail]);
        });
      });
    });
  });
});

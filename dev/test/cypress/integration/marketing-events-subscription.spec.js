'use strict';

describe('Marketing Events', function() {
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

  before(() => {
    cy.task('setConfig', { collectMarketingEvents: 'enabled' });
  });

  context('guest with double optin off', function() {
    it('should create subscription events', function() {
      const guestEmail = 'event.doptin-off.sub@guest-cypress.com';
      subscribe(guestEmail);

      cy.wait(1000);
      cy.shouldCreateEvent('newsletter_send_confirmation_success_email', {
        subscriber: { subscriber_email: guestEmail }
      });
      cy.shouldNotShowErrorMessage();
      cy.isSubscribed(guestEmail);

      unsubscribe(guestEmail);

      cy.shouldCreateEvent('newsletter_send_unsubscription_email', {
        subscriber: { subscriber_email: guestEmail }
      });
      cy.shouldNotShowErrorMessage();
      cy.isNotSubscribed(guestEmail);
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

    it('should create newsletter_send_confirmation_request_email event', function() {
      const guestEmail = 'event.doptin-on.sub@guest-cypress.com';
      subscribe(guestEmail);

      cy.wait(1000);
      cy.shouldCreateEvent('newsletter_send_confirmation_request_email', {
        subscriber: { subscriber_email: guestEmail }
      });
      cy.shouldNotShowErrorMessage();
      cy.isSubscribed(guestEmail, true);

      unsubscribe(guestEmail);

      cy.shouldCreateEvent('newsletter_send_unsubscription_email', {
        subscriber: { subscriber_email: guestEmail }
      });
      cy.shouldNotShowErrorMessage();
      cy.isNotSubscribed(guestEmail);
    });
  });
});

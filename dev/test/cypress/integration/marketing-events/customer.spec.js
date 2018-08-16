'use strict';

describe('Marketing Events', function() {
  context('with settings off', function () {
    it('should create newsletter_send_confirmation_success_email event', function() {
      const guestEmail = 'guest.email2@guest.com';
      cy.visit('http://web/');

      cy.get('#newsletter').type(guestEmail);
      cy.get('.action.subscribe.primary[type="submit"]').click();

      cy.shouldNotExistsEvents();
    });
  });

  context('with settings on', function () {
    it('should create newsletter_send_confirmation_success_email event', function() {
      cy.task('setConfig', { websiteId: 1, config: { collectMarketingEvents: 'enabled' } });

      const guestEmail = 'guest.email@guest.com';
      cy.visit('http://web/');

      cy.get('#newsletter').type(guestEmail);
      cy.get('.action.subscribe.primary[type="submit"]').click();

      cy.shouldCreateEvent('newsletter_send_confirmation_success_email', {
        confirmation_link: { subscriber_email: guestEmail }
      });
    });
  });
});

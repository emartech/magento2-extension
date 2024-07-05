'use strict';

describe('Marketing Events', function () {
  beforeEach(() => {
    cy.task('clearMails');
    cy.task('getDefaultCustomer').as('defaultCustomer');
  });

  afterEach(() => {
    cy.logout();
  });

  context('magentoSendEmails config is disabled', function () {
    before(() => {
      cy.task('setConfig', {
        collectMarketingEvents: 'enabled',
        magentoSendEmail: 'disabled'
      });
      cy.task('clearEvents');
    });

    it('should create customer_password_reset event', function () {
      const newPassword = 'newPassword2';

      cy.loginWithCustomer(this.defaultCustomer);
      cy.changeCredentials(this.defaultCustomer.password, { password: newPassword });
      cy.task('setDefaultCustomerProperty', { password: newPassword });

      cy.shouldCreateEvent('customer_password_reset', {
        new_customer_email: this.defaultCustomer.email
      });
      cy.shouldNotShowErrorMessage();

      cy.task('getSentAddresses').then(emailAddresses => {
        expect(emailAddresses).to.be.empty;
      });
    });

    it('should create customer_email_changed event', function () {
      const newEmail = 'cypress3@default.com';

      cy.loginWithCustomer(this.defaultCustomer);
      cy.changeCredentials(this.defaultCustomer.password, { email: newEmail });
      cy.task('setDefaultCustomerProperty', { email: newEmail });

      cy.shouldCreateEvent('customer_email_changed', {
        new_customer_email: newEmail
      });
      cy.shouldNotShowErrorMessage();

      cy.task('getSentAddresses').then(emailAddresses => {
        expect(emailAddresses).to.be.empty;
      });
    });

    it('should create customer_email_and_password_changed event', function () {
      const newEmail = 'cypress4@default.com';
      const newPassword = 'newPassword3';

      cy.loginWithCustomer(this.defaultCustomer);
      cy.changeCredentials(this.defaultCustomer.password, { password: newPassword, email: newEmail });
      cy.task('setDefaultCustomerProperty', { email: newEmail, password: newPassword });

      cy.shouldCreateEvent('customer_email_and_password_changed', {
        new_customer_email: newEmail
      });
      cy.shouldNotShowErrorMessage();

      cy.task('getSentAddresses').then(emailAddresses => {
        expect(emailAddresses).to.be.empty;
      });
    });
  });

  context('magentoSendEmails config is enabled', function () {
    before(() => {
      cy.task('setConfig', {
        collectMarketingEvents: 'enabled',
        magentoSendEmail: 'enabled'
      });
      cy.task('clearEvents');
    });

    it('should create customer_password_reset event', function () {
      const newPassword = 'newPassword2';

      cy.loginWithCustomer(this.defaultCustomer);
      cy.changeCredentials(this.defaultCustomer.password, { password: newPassword });
      cy.task('setDefaultCustomerProperty', { password: newPassword });

      cy.shouldCreateEvent('customer_password_reset', {
        new_customer_email: this.defaultCustomer.email
      });
      cy.shouldNotShowErrorMessage();

      cy.task('getSentAddresses').then(emailAddresses => {
        expect(emailAddresses).to.be.eql([this.defaultCustomer.email]);
      });
    });

    it('should create customer_email_changed event', function () {
      const oldEmail = this.defaultCustomer.email;
      const newEmail = 'cypress3@default.com';

      cy.loginWithCustomer(this.defaultCustomer);
      cy.changeCredentials(this.defaultCustomer.password, { email: newEmail });
      cy.task('setDefaultCustomerProperty', { email: newEmail });

      cy.shouldCreateEvent('customer_email_changed', {
        new_customer_email: newEmail
      });
      cy.shouldNotShowErrorMessage();

      cy.task('getSentAddresses').then(emailAddresses => {
        expect(emailAddresses).to.be.eql([newEmail, oldEmail]);
      });
    });

    it('should create customer_email_and_password_changed event', function () {
      const oldEmail = this.defaultCustomer.email;
      const newEmail = 'cypress4@default.com';
      const newPassword = 'newPassword3';

      cy.loginWithCustomer(this.defaultCustomer);
      cy.changeCredentials(this.defaultCustomer.password, { password: newPassword, email: newEmail });
      cy.task('setDefaultCustomerProperty', { email: newEmail, password: newPassword });

      cy.shouldCreateEvent('customer_email_and_password_changed', {
        new_customer_email: newEmail
      });
      cy.shouldNotShowErrorMessage();

      cy.task('getSentAddresses').then(emailAddresses => {
        expect(emailAddresses).to.be.eql([newEmail, oldEmail]);
      });
    });
  });
});

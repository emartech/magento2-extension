'use strict';

const merchantId = 'merchantId123';
const webTrackingSnippetUrl = Cypress.env('snippetUrl');
const predictUrl = `http://cdn.scarabresearch.com/js/${merchantId}/scarab-v2.js`;

describe('Web extend scripts', function () {
  before(() => {
    cy.task('setConfig', {
      injectSnippet: 'enabled',
      merchantId,
      webTrackingSnippetUrl
    });
    cy.task('flushMagentoCache');
  });

  beforeEach(() => {
    cy.on('window:before:load', win => {
      win.Emarsys = { Magento2: { track() {} } };
      win.Emarsys.Magento2.track = cy.stub().as('track');
    });

    cy.on('window:load', win => {
      win.customerStub = cy.stub().as('customerStub');

      const testScriptNode = win.document.createElement('script');
      testScriptNode.text = `window.require(['Magento_Customer/js/customer-data'], function (customerData) {
        window.customerStub(customerData.get('customer')())
        customerData.get('customer').subscribe(function (customer) {
          window.customerStub(customer);
        });
      });`;
      win.document.head.appendChild(testScriptNode);
    });
  });

  afterEach(() => {
    cy.logout();
  });

  it('should include web-extend scripts', function () {
    cy.visit('/');

    cy.get('script').then(scripts => {
      const sources = [...scripts].map(script => script.src);
      expect(sources).to.include(predictUrl);
      expect(sources).to.include(webTrackingSnippetUrl);
    });
  });

  it('should include proper customer data', function () {
    cy.loginWithCustomer({ email: 'roni_cost@example.com', password: 'roni_cost3@example.com' });
    cy.visit('/fusion-backpack.html');

    cy.get('@customerStub').should('be.calledWithMatch', {
      fullname: 'Veronica Costello',
      firstname: 'Veronica',
      id: '1',
      email: 'roni_cost@example.com'
    });
  });

  it('should include orderData after ordering as a guest', function () {
    cy.visit('/fusion-backpack.html');

    cy.get('.loading-mask').should('not.exist');
    cy.get('.input-text.qty')
      .clear()
      .type('2');

    cy.get('#product-addtocart-button').click();
    cy.get('.counter-number').should('contain', '2');

    cy.get('.action.showcart').click();
    cy.get('#top-cart-btn-checkout').click();

    cy.visit('/checkout');

    cy.get('#checkout-step-shipping input.input-text[name="username"]').type('guest@cypress.net');
    cy.get('#checkout-step-shipping input.input-text[name="firstname"]').type('Guest');
    cy.get('#checkout-step-shipping input.input-text[name="lastname"]').type('Da Best');
    cy.get('#checkout-step-shipping input.input-text[name="street[0]"]').type('Cloverfield lane 1');
    cy.get('#checkout-step-shipping input.input-text[name="city"]').type('Nowhere');
    cy.get('#checkout-step-shipping select.select[name="country_id"]').select('HU');
    cy.get('#checkout-step-shipping input.input-text[name="postcode"]').type('2800');
    cy.get('#checkout-step-shipping input.input-text[name="telephone"]').type('0036905556969');

    cy.get('.table-checkout-shipping-method input[type="radio"][value="flatrate_flatrate"][checked="true"]');
    cy.get('button[data-role="opc-continue"]').click();

    cy.get('button[title="Place Order"]').click();
    cy.get('.checkout-success');

    cy.window().then(win => {
      const orderData = win.Emarsys.Magento2.orderData;
      expect(orderData.orderId).to.be.not.undefined;
      expect(orderData.items).to.be.eql([
        {
          item: '24-MB02',
          price: 118,
          quantity: 2
        }
      ]);
      expect(orderData.email).to.be.equal('guest@cypress.net');
    });
  });

  it('should include orderData after ordering as a logged in user', function () {
    cy.loginWithCustomer({ email: 'roni_cost@example.com', password: 'roni_cost3@example.com' });
    cy.visit('/fusion-backpack.html');

    cy.get('.loading-mask').should('not.exist');
    cy.get('.input-text.qty')
      .clear()
      .type('2');

    cy.get('#product-addtocart-button').click();
    cy.get('.counter-number').should('contain', '2');

    cy.get('.action.showcart').click();
    cy.get('#top-cart-btn-checkout').click();

    cy.get('.table-checkout-shipping-method input[type="radio"][value="flatrate_flatrate"]').check();
    cy.get('button[data-role="opc-continue"]').click();

    cy.get('button[title="Place Order"]').click();
    cy.get('.checkout-success');

    cy.window().then(win => {
      const orderData = win.Emarsys.Magento2.orderData;
      expect(orderData.orderId).to.be.not.undefined;
      expect(orderData.items).to.be.eql([
        {
          item: '24-MB02',
          price: 118,
          quantity: 2
        }
      ]);
      expect(orderData.email).to.be.equal('roni_cost@example.com');
    });
  });
});

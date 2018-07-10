'use strict';

const customers = [
  {
    group_id: 0,
    dob: '1977-11-12',
    email: 'yolo@customer.net',
    firstname: 'Yolo',
    lastname: 'World',
    store_id: 1,
    website_id: 1,
    disable_auto_group_change: 0
  },
  {
    group_id: 0,
    dob: '1977-11-12',
    email: 'yolo2@customer.net',
    firstname: 'Yolo2',
    lastname: 'World',
    store_id: 1,
    website_id: 1,
    disable_auto_group_change: 0
  }
];

describe('Customers endpoint', function() {
  before(async function() {
    for (const customer of customers) {
      await this.createCustomer(customer);
    }
  });

  after(async function() {
    await this.db.raw('DELETE FROM customer_entity where email like "%@customer.net"');
  });

  afterEach(async function() {});

  it('returns customers according to page and page_size inlcuding last_page', async function() {
    const page = 1;
    const limit = 2;

    const { customers, lastPage } = await this.magentoApi.execute('customers', 'getAll', { page, limit });
    const customer = customers[0];

    expect(customers.length).to.equal(2);
    expect(customer.entity_id).to.equal('1');
    expect(lastPage).to.equal(2);

    expect(customer.email).to.be.a('string');
    expect(customer.firstname).to.be.a('string');
    expect(customer.lastname).to.be.a('string');
    expect(customer.billing_address).to.be.an('object');
    expect(customer.shipping_address).to.be.an('object');
    expect(customer).to.have.property('subscription');
    expect(customer).to.have.property('billing_address');
    expect(customer).to.have.property('shipping_address');
  });
});

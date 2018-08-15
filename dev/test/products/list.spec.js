'use strict';

describe('Products endpoint', function() {
  before(function() {});

  afterEach(async function() {});

  it('returns product count and products according to page and page_size', async function() {
    const page = 3;
    const limit = 10;

    const { products, productCount } = await this.magentoApi.execute('products', 'get', { page, limit });
    const product = products[0];

    expect(products.length).to.equal(10);
    expect(product.entity_id).to.equal('21');
    expect(productCount).to.equal(2048);

    expect(product.type).to.equal('simple');
    expect(product.children_entity_ids).to.be.an('array');
    expect(product.categories[0]).to.have.members(['Gear']);
    expect(product.categories[1]).to.have.ordered.members(['Gear', 'Fitness Equipment']);
    expect(product.sku).to.equal('24-WG084');
    expect(product.name).to.equal('Sprite Foam Yoga Brick');
    expect(product.price).to.equal('5.0000');
    expect(product.link).to.include('/index.php/sprite-foam-yoga-brick.html');
    expect(product.images).to.eql({
      image: 'http://web/pub/media/catalog/product/l/u/luma-yoga-brick.jpg',
      small_image: 'http://web/pub/media/catalog/product/l/u/luma-yoga-brick.jpg',
      thumbnail: 'http://web/pub/media/catalog/product/l/u/luma-yoga-brick.jpg'
    });
    expect(product.qty).to.equal('100.0000');
    expect(product.is_in_stock).to.equal('1');
    expect(product.status).to.equal('1');
    expect(product.description).to.equal(
      // eslint-disable-next-line
      `<p>Our top-selling yoga prop, the 4-inch, high-quality Sprite Foam Yoga Brick is popular among yoga novices and studio professionals alike. An essential yoga accessory, the yoga brick is a critical tool for finding balance and alignment in many common yoga poses. Choose from 5 color options.</p>\n<ul>\n<li>Standard Large Size: 4\" x 6\" x 9\".\n<li>Beveled edges for ideal contour grip.\n<li>Durable and soft, scratch-proof foam.\n<li>Individually wrapped.\n<li>Ten color choices.\n</ul> `
    );
  });

  it('returns child entities for configurable products', async function() {
    const page = 67;
    const limit = 1;

    const { products } = await this.magentoApi.execute('products', 'get', { page, limit });
    const product = products[0];

    expect(product.type).to.equal('configurable');
    expect(product.children_entity_ids).to.eql([
      '52',
      '53',
      '54',
      '55',
      '56',
      '57',
      '58',
      '59',
      '60',
      '61',
      '62',
      '63',
      '64',
      '65',
      '66'
    ]);
  });
});

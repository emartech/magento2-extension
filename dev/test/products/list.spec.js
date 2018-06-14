'use strict';

describe('Products endpoint', function() {
  before(function() {});

  afterEach(async function() {});

  it('returns products', async function() {
    const page = 1;
    const pageSize = 10;

    const { products } = (await this.magentoApi.execute('products', 'get', page, pageSize))[0];
    const product = products[0];

    expect(product.type).to.equal('simple');
    expect(product.entity_id).to.be.a('string');
    expect(product.children_entity_ids).to.be.an('array');
    expect(product.categories[0]).to.have.members(['Gear']);
    expect(product.categories[1]).to.have.ordered.members(['Gear', 'Bags']);
    expect(product.sku).to.equal('24-MB01');
    expect(product.name).to.equal('Joust Duffle Bag');
    expect(product.price).to.equal('34.0000');
    expect(product.link).to.include('http://magento.local:8888/index.php/joust-duffle-bag.html');
    expect(product.images).to.eql({
      image: 'http://magento.local:8888/pub/media/catalog/product/m/b/mb01-blue-0.jpg',
      small_image: 'http://magento.local:8888/pub/media/catalog/product/m/b/mb01-blue-0.jpg',
      thumbnail: 'http://magento.local:8888/pub/media/catalog/product/m/b/mb01-blue-0.jpg'
    });
    expect(product.qty).to.equal('100.0000');
    expect(product.is_in_stock).to.equal('1');
    // eslint-disable-next-line
    expect(product.description).to.equal(`<p>The sporty Joust Duffle Bag can't be beat - not in the gym, not on the luggage carousel, not anywhere. Big enough to haul a basketball or soccer ball and some sneakers with plenty of room to spare, it's ideal for athletes with places to go.<p>\n<ul>\n<li>Dual top handles.</li>\n<li>Adjustable shoulder strap.</li>\n<li>Full-length zipper.</li>\n<li>L 29\" x W 13\" x H 11\".</li>\n</ul>`
    );
  });
});

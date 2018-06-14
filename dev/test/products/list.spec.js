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
    expect(product.categories).to.be.an('array');
    expect(product.sku).to.equal('DEFAULT-SKU');
    expect(product.name).to.equal('Default product');
    expect(product.price).to.equal('69.0000');
    expect(product.link).to.include('http://magento.local:8888/index.php/default-product.html');
    expect(product.images).to.eql({
      image: 'http://magento.local:8888/pub/media/catalog/product',
      small_image: 'http://magento.local:8888/pub/media/catalog/product',
      thumbnail: 'http://magento.local:8888/pub/media/catalog/product'
    });
    expect(product.qty).to.equal('999.0000');
    expect(product.is_in_stock).to.equal('1');
    expect(product.description).to.equal('Default products description');
  });
});

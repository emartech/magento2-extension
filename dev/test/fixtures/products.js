'use strict';

const productFactory = productData => {
  const defaultProductData = {
    sku: 'DEFAULT-SKU',
    name: 'Default product',
    custom_attributes: {
      description: 'Default products description',
      short_description: 'Such short, very description'
    },
    price: 69.0,
    status: 1,
    visibility: 4,
    type_id: 'simple',
    attribute_set_id: 4,
    weight: 1,
    extension_attributes: {
      stock_item: {
        stock_id: 1,
        qty: 999,
        is_in_stock: 1
      }
    }
  };

  return Object.assign({}, defaultProductData, productData);
};

const defaultProduct = productFactory({});

const productsForProductSync = [productFactory({ sku: 'PRODUCT-SYNC-SKU', name: 'Product For Product Sync' })];

module.exports = {
  defaultProduct,
  productsForProductSync
};

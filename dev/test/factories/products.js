'use strict';

const productFactory = productData => {
  const defaultProductData = {
    sku: 'DEFAULT-SKU',
    name: 'Default product',
    custom_attributes: [
      {
        attribute_code: 'description',
        value: 'Default products description'
      }
    ],
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

module.exports = {
  productFactory
};

'use strict';

const cartItem = async (magentoApi) => {
  const product = await magentoApi.get({
    path: 'index.php/default/rest/all/V1/products/WS03'
  });

  const options = [
    {
      option_id: product.extension_attributes.configurable_product_options[0].attribute_id,
      option_value: product.extension_attributes.configurable_product_options[0].values[0].value_index
    },
    {
      option_id: product.extension_attributes.configurable_product_options[1].attribute_id,
      option_value: product.extension_attributes.configurable_product_options[1].values[0].value_index
    }
  ];

  return {
    sku: 'WS03',
    qty: 2,
    product_type: 'configurable',
    product_option: {
      extension_attributes: {
        configurable_item_options: options
      }
    }
  };
};

module.exports = { get: cartItem };

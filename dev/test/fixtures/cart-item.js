'use strict';

const cartItem = (magentoVersion, magentoEdition) => {
  const defaultOptions = [
    {
      option_id: 93,
      option_value: 50
    },
    {
      option_id: 145,
      option_value: 167
    }
  ];

  const versionOptions = {
    Community: {
      '2.1.8': [
        {
          option_id: 93,
          option_value: 50
        },
        {
          option_id: 142,
          option_value: 167
        }
      ],
      '2.3.3': [
        {
          option_id: 93,
          option_value: 5477
        },
        {
          option_id: 150,
          option_value: 5594
        }
      ],
      '2.3.4': [
        {
          option_id: 93,
          option_value: 5477
        },
        {
          option_id: 152,
          option_value: 5593
        }
      ],
      '2.3.5': [
        {
          option_id: 93,
          option_value: 5477
        },
        {
          option_id: 145,
          option_value: 5593
        }
      ],
      '2.4.0': [
        {
          option_id: 93,
          option_value: 50
        },
        {
          option_id: 144,
          option_value: 166
        }
      ]
    },
    Enterprise: {
      '2.1.9': [
        {
          option_id: 93,
          option_value: 59
        },
        {
          option_id: 184,
          option_value: 179
        }
      ],
      '2.3.1': [
        {
          option_id: 93,
          option_value: 59
        },
        {
          option_id: 187,
          option_value: 179
        }
      ],
      '2.3.2': [
        {
          option_id: 93,
          option_value: 59
        },
        {
          option_id: 187,
          option_value: 179
        }
      ],
      '2.3.3': [
        {
          option_id: 93,
          option_value: 5486
        },
        {
          option_id: 192,
          option_value: 5603
        }
      ]
    }
  };

  const options = versionOptions[magentoEdition][magentoVersion]
    ? versionOptions[magentoEdition][magentoVersion]
    : defaultOptions;

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

'use strict';

const getProducts = (hostname, page, limit, magentoVersion, magentoEdition) => {
  return {
    products: products(hostname, page, limit, magentoVersion, magentoEdition, magentoEdition),
    productCount: magentoEdition === 'Enterprise' ? 2048 : 2046
  };
};

const products = (hostname, page, limit, magentoVersion, magentoEdition) => {
  if (page === 3 && limit === 10) {
    return [
      {
        entity_id: 21,
        type: 'simple',
        children_entity_ids: [],
        categories: ['1/2/3', '1/2/3/5'],
        sku: '24-WG084',
        qty: 100,
        is_in_stock: 1,
        images: {
          image: `http://${hostname}/pub/media/catalog/product/l/u/luma-yoga-brick.jpg`,
          small_image: `http://${hostname}/pub/media/catalog/product/l/u/luma-yoga-brick.jpg`,
          thumbnail: `http://${hostname}/pub/media/catalog/product/l/u/luma-yoga-brick.jpg`
        },
        store_data: [
          {
            name: 'Sprite Foam Yoga Brick',
            price: 2,
            webshop_price: 2,
            original_webshop_price: 5,
            original_display_price: 10,
            display_webshop_price: 4,
            link: `http://${hostname}/index.php/default/sprite-foam-yoga-brick.html`,
            status: 1,
            // eslint-disable-next-line
            description: `<p>Our top-selling yoga prop, the 4-inch, high-quality Sprite Foam Yoga Brick is popular among yoga novices and studio professionals alike. An essential yoga accessory, the yoga brick is a critical tool for finding balance and alignment in many common yoga poses. Choose from 5 color options.</p>\n<ul>\n<li>Standard Large Size: 4\" x 6\" x 9\".\n<li>Beveled edges for ideal contour grip.\n<li>Durable and soft, scratch-proof foam.\n<li>Individually wrapped.\n<li>Ten color choices.\n</ul> `
          }
        ]
      },
      {},
      {},
      {},
      {},
      {},
      {},
      {},
      {},
      {}
    ];
  }

  if (page === 67 && limit === 1) {
    return configurable[magentoVersion][magentoEdition];
  }
};

const configurable = {
  '2.1.8': {
    Community: [
      {
        type: 'configurable',
        children_entity_ids: [52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66]
      }
    ]
  },
  '2.1.9': {
    Enterprise: [
      {
        type: 'configurable',
        children_entity_ids: [54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68]
      }
    ]
  },
  '2.2.6': {
    Community: [
      {
        type: 'configurable',
        children_entity_ids: [52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66]
      }
    ]
  },
  '2.3.0': {
    Community: [
      {
        type: 'configurable',
        children_entity_ids: [53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67]
      }
    ]
  },
  '2.3.1': {
    Community: [
      {
        type: 'configurable',
        children_entity_ids: [53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67]
      }
    ],
    Enterprise: [
      {
        type: 'configurable',
        children_entity_ids: [55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69]
      }
    ]
  },
  '2.3.2': {
    Community: [
      {
        type: 'configurable',
        children_entity_ids: [53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67]
      }
    ],
    Enterprise: [
      {
        type: 'configurable',
        children_entity_ids: [53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67]
      }
    ]
  },
  '2.3.3': {
    Community: [
      {
        type: 'configurable',
        children_entity_ids: [53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67]
      }
    ],
    Enterprise: [
      {
        type: 'configurable',
        children_entity_ids: [53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67]
      }
    ]
  },
  '2.3.4': {
    Community: [
      {
        type: 'configurable',
        children_entity_ids: [53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67]
      }
    ],
    Enterprise: [
      {
        type: 'configurable',
        children_entity_ids: [53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67]
      }
    ]
  },
  '2.3.5': {
    Community: [
      {
        type: 'configurable',
        children_entity_ids: [53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67]
      }
    ],
    Enterprise: [
      {
        type: 'configurable',
        children_entity_ids: [53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67]
      }
    ]
  },
  '2.4.0': {
    Community: [
      {
        type: 'configurable',
        children_entity_ids: [53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67]
      }
    ],
    Enterprise: [
      {
        type: 'configurable',
        children_entity_ids: [53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67]
      }
    ]
  }
};

module.exports = {
  getProducts
};

'use strict';

const shipOrder = async (magentoApi, orderId) => {
  await magentoApi.post({
    path: `/index.php/rest/V1/order/${orderId}/ship`,
    payload: {
      notify: true
    }
  });
};

const invoiceOrder = async (magentoApi, orderId) => {
  await magentoApi.post({
    path: `/index.php/rest/V1/order/${orderId}/invoice`,
    payload: {
      capture: true,
      notify: true
    }
  });
};

const localAddresses = {
  shipping_address: {
    region: 'New York',
    region_id: 43,
    region_code: 'NY',
    country_id: 'US',
    street: ['123 Oak Ave'],
    postcode: '10577',
    city: 'Purchase',
    firstname: 'Jane',
    lastname: 'Doe',
    email: 'jdoe@example.shipping.com',
    telephone: '512-555-1111'
  },
  billing_address: {
    region: 'New York',
    region_id: 43,
    region_code: 'NY',
    country_id: 'US',
    street: ['123 Oak Ave'],
    postcode: '10577',
    city: 'Purchase',
    firstname: 'Jane',
    lastname: 'Doe',
    email: 'jdoe@example.billing.com',
    telephone: '512-555-1111'
  },
  shipping_carrier_code: 'flatrate',
  shipping_method_code: 'flatrate'
};

const createNewCustomerOrder = async (magentoApi, customer, localCartItem) => {
  const { data: cartId } = await magentoApi.post({
    path: `/index.php/default/rest/V1/customers/${customer.entityId}/carts`
  });

  await magentoApi.post({
    path: `/index.php/rest/V1/carts/${cartId}/items`,
    payload: {
      cartItem: Object.assign(localCartItem, { quote_id: cartId })
    }
  });

  await magentoApi.post({
    path: `/index.php/rest/V1/carts/${cartId}/shipping-information`,
    payload: {
      addressInformation: localAddresses
    }
  });

  const { data: orderId } = await magentoApi.put({
    path: `/index.php/rest/V1/carts/${cartId}/order`,
    payload: {
      paymentMethod: {
        method: 'checkmo'
      }
    }
  });

  return { cartId, orderId };
};

const createNewGuestOrder = async (magentoApi, localCartItem) => {
  const addressInformation = {
    shippingAddress: {
      region: 'New York',
      region_id: 43,
      region_code: 'NY',
      country_id: 'US',
      street: ['123 Oak Ave'],
      postcode: '10577',
      city: 'Purchase',
      firstname: 'Jane',
      lastname: 'Doe',
      email: 'jdoe@example.shipping.com',
      telephone: '512-555-1111'
    },
    billingAddress: {
      region: 'New York',
      region_id: 43,
      region_code: 'NY',
      country_id: 'US',
      street: ['123 Oak Ave'],
      postcode: '10577',
      city: 'Purchase',
      firstname: 'Jane',
      lastname: 'Doe',
      email: 'jdoe@example.billing.com',
      telephone: '512-555-1111'
    },
    shipping_carrier_code: 'flatrate',
    shipping_method_code: 'flatrate'
  };

  const { data: cartId } = await magentoApi.post({
    path: '/index.php/rest/V1/guest-carts'
  });

  await magentoApi.post({
    path: `/index.php/rest/V1/guest-carts/${cartId}/items`,
    payload: {
      cartItem: { ...localCartItem, quote_id: cartId }
    }
  });

  await magentoApi.post({
    path: `/index.php/rest/V1/guest-carts/${cartId}/shipping-information`,
    payload: {
      addressInformation
    }
  });

  const { data: orderId } = await magentoApi.put({
    path: `/index.php/rest/V1/guest-carts/${cartId}/order`,
    payload: {
      paymentMethod: {
        method: 'checkmo'
      }
    }
  });

  return { cartId, orderId };
};

const commentOrder = async (magentoApi, orderId) => {
  await magentoApi.post({
    path: `/index.php/rest/V1/orders/${orderId}/comments`,
    payload: {
      statusHistory: {
        comment: 'Comment on order',
        is_customer_notified: 1
      }
    }
  });
};

const refundOrder = async (magentoApi, orderId) => {
  await magentoApi.post({
    path: `/index.php/rest/V1/order/${orderId}/refund`,
    payload: {
      notify: true
    }
  });
};

const changeOrderStatus = async (magentoApi, orderId, orderStatus, orderState) => {
  await magentoApi.post({
    path: '/index.php/rest/V1/orders',
    payload: {
      entity: {
        entity_id: orderId,
        status: orderStatus,
        state: orderState
      }
    }
  });
};

const refundOnePieceFromFirstItemOfOrder = async (magentoApi, orderId) => {
  const { items } = await magentoApi.get({ path: `/index.php/rest/V1/orders/${orderId}` });
  const itemId = items[0].item_id;
  const refundedSku = items[0].sku;

  await magentoApi.post({
    path: `/index.php/rest/V1/order/${orderId}/refund`,
    payload: {
      items: [
        {
          order_item_id: itemId,
          qty: 1
        }
      ],
      notify: false
    }
  });

  return refundedSku;
};

const fulfillOrder = async (magentoApi, orderId) => {
  await invoiceOrder(magentoApi, orderId);

  await shipOrder(magentoApi, orderId);
};

module.exports = {
  shipOrder,
  invoiceOrder,
  createNewGuestOrder,
  createNewCustomerOrder,
  commentOrder,
  refundOrder,
  localAddresses,
  changeOrderStatus,
  refundOnePieceFromFirstItemOfOrder,
  fulfillOrder
};

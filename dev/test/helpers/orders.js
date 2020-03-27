'use strict';

const shipOrder = async (magentoApi, orderId) => {
  await magentoApi.post({
    path: `/index.php/rest/V1/order/${orderId}/ship`,
    payload: {
      notify: true
    }
  });
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

module.exports = {
  shipOrder,
  createNewGuestOrder
};

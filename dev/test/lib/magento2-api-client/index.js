'use strict';

const axios = require('axios');

class Magento2ApiClient {
  constructor({ token }) {
    this.token = token;
    this.baseUrl = process.env.MAGENTO_URL;
  }
  async get({ url }) {
    const response = await axios.get(`http://web${url}`, {
      headers: { Authorization: `Bearer ${this.token}` }
    });

    return response;
  }
  async post({ url, payload }) {
    const response = await axios.post(`http://web${url}`, payload, {
      headers: { Authorization: `Bearer ${this.token}` }
    });

    return response;
  }
  static create({ token }) {
    return new Magento2ApiClient({ token });
  }
}

module.exports = Magento2ApiClient;

'use strict';

const axios = require('axios');

const getMails = async () => {
  return await axios.get('http://mailhog:8025/api/v1/messages');
};

const getSentAddresses = async () => {
  const { data: emails } = await getMails();
  return emails.map(email => {
    const rawEmailTo = email.Raw.To[0];
    console.log('getSentAddresses -> rawEmailTo', rawEmailTo);
    return email.Raw.To[0].split('<')[1];
  });
};

const clearMails = async () => {
  return await axios.delete('http://mailhog:8025/api/v1/messages');
};

module.exports = { getMails, getSentAddresses, clearMails };

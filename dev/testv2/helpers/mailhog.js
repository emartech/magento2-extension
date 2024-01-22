'use strict';

const axios = require('axios');

const mailhogUrl = 'http://mailhog:8025';

const getMails = async () => {
  return await axios.get(`${mailhogUrl}/api/v1/messages`);
};

const getSentAddresses = async () => {
  const { data: emails } = await getMails();
  return emails.map(email => {
    let emailTo = email.Raw.To[0];
    if (emailTo.includes('<')) {
      emailTo = emailTo.split('<')[1];
    }
    return emailTo;
  });
};

const clearMails = async () => {
  return await axios.delete(`${mailhogUrl}/api/v1/messages`);
};

module.exports = { getMails, getSentAddresses, clearMails };

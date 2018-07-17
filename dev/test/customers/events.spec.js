'use strict';

const http = require('http');
const axios = require('axios');
const FormData = require('form-data');

const getCookie = async () => {
  return new Promise(resolve => {
    const opts = { host: 'magento.local', path: '/index.php/customer/account/forgotpassword/', port: 80 };
    http.get(opts, function(res) {
      const result = res.headers['set-cookie'].map(h => h.split(';')[0]).join('; ');
      resolve(result);
    });
  });
};

describe('Customer events', function() {
  it('should get session id and post reset password form', async function() {
    const cookie = await getCookie();
    const formData = new FormData();
    formData.append('email', 'test@emarsys.com');

    try {
      await axios.post(
        'http://magento.local/index.php/customer/account/forgotpasswordpost/',
        formData,
        {
          maxRedirects: 0,
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            Cookie: cookie
          }
        }
      );
    } catch (error) {
      console.log(error.response.headers);
    }

    return true;
  });
});

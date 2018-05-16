'use strict';

module.exports = {
  server: {
    test: {
      filePatterns: ['./setup.spec.js', '!(node_modules)/**/*.spec.js']
    }
  }
};

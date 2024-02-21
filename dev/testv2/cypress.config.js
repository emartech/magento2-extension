const { defineConfig } = require('cypress');

module.exports = defineConfig({
    video: false,
    trashAssetsBeforeRuns: false,
    requestTimeout: 30000,
    defaultCommandTimeout: 30000,
    pageLoadTimeout: 30000,
    blockHosts: ['*snippet.url.com', '*scarabresearch.com'],
    env: {
        snippetUrl: 'http://snippet.url.com/main.js'
    },
    e2e: {
        // We've imported your old cypress plugins here.
        // You may want to clean this up later by importing these.
        setupNodeEvents(on, config) {
            return require('./cypress/plugins/index.js')(on, config);
        },
        baseUrl: 'http://magento-test.local/index.php/default/',
        specPattern: 'cypress/integration/**/*.spec.js'
    }
});

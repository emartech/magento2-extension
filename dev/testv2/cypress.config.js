const { defineConfig } = require('cypress');

module.exports = defineConfig({
    video: false,
    trashAssetsBeforeRuns: false,
    requestTimeout: 120000,
    defaultCommandTimeout: 120000,
    pageLoadTimeout: 120000,
    experimentalMemoryManagement: true,
    experimentalModifyObstructiveThirdPartyCode: true,
    modifyObstructiveCode: true,
    numTestsKeptInMemory: 0,
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

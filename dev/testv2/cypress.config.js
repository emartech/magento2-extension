const { defineConfig } = require('cypress');

module.exports = defineConfig({
    video: false,
    trashAssetsBeforeRuns: false,
    requestTimeout: 120000,
    defaultCommandTimeout: 120000,
    pageLoadTimeout: 120000,
    experimentalMemoryManagement: true,
    blockHosts: ['*snippet.url.com', '*scarabresearch.com'],
    env: {
        snippetUrl: 'http://snippet.url.com/main.js'
    },
    e2e: {
        // We've imported your old cypress plugins here.
        // You may want to clean this up later by importing these.
        setupNodeEvents(on, config) {
            on("before:browser:launch", (browser, launchOptions) => {
                if (["chrome", "edge"].includes(browser.name)) {
                    if (browser.isHeadless) {
                        launchOptions.args.push("--no-sandbox");
                        launchOptions.args.push("--disable-gl-drawing-for-tests");
                        launchOptions.args.push("--disable-gpu");
                    }
                    launchOptions.args.push("--js-flags=--max-old-space-size=3500");
                }
                return launchOptions;
            });

            return require('./cypress/plugins/index.js')(on, config);
        },
        baseUrl: 'http://magento-test.local/index.php/default/',
        specPattern: 'cypress/integration/**/*.spec.js'
    }
});

'use strict';

require('./commands');

before(() => {
  Cypress.env('RETRIES', 3);
  cy.task('cacheTablePrefix');
});

Cypress.on('uncaught:exception', (err, runnable) => { // eslint-disable-line no-unused-vars
  console.log('uncaught:exception', err.toString());
  return false;
});

let logs = '';

Cypress.on('window:before:load', window => {
/*
  const docIframe = window.parent.document.getElementById("Your App: 'test'");
  const appWindow = docIframe.contentWindow;

  ['log', 'info', 'error', 'warn', 'debug'].forEach(consoleProperty => {
    appWindow.console[consoleProperty] = function(...args) {
      logs += args.join(' ') + '\n';
    };
  });
 */
});

Cypress.mocha.getRunner().on('test', () => {
  logs = '';
});

Cypress.on('fail', error => {
  if (!error) {
    error = '';
  }
  error.stack += '\nConsole Logs:\n========================\n';
  error.stack += logs;
  logs = '';
  throw error;
});

console.log = function(...args) {
  logs += args.join('\n');
  logs += '\n';
};

Cypress.Commands.overwrite('log', (originalFn, ...args) => {
  logs += args.join('\n');
  logs += '\n';
  originalFn(...args);
});

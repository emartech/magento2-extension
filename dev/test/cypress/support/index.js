'use strict';

require('./commands');

afterEach(() => {
  cy.task('clearEvents');
});

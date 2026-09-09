'use strict';

const { FlatCompat } = require('@eslint/eslintrc');
const globals = require('globals');
const emarsysConfig = require('eslint-config-emarsys');
const mochaPlugin = require('eslint-plugin-mocha').default;

const compat = new FlatCompat({ baseDirectory: __dirname });

module.exports = [
  {
    ignores: ['node_modules/**']
  },
  ...compat.config(emarsysConfig),
  {
    plugins: {
      mocha: mochaPlugin
    },
    languageOptions: {
      ecmaVersion: 9,
      sourceType: 'commonjs',
      globals: {
        ...globals.es2015,
        ...globals.node,
        ...globals.mocha,
        ...globals.browser,
        inject: 'writable',
        onmessage: 'writable',
        expect: 'writable',
        describe: 'writable'
      }
    },
    rules: {
      curly: [2, 'multi-line'],
      'new-cap': 0,
      'no-unused-expressions': 0,
      'operator-linebreak': 'off',
      'require-yield': 0,
      'security/detect-child-process': 0,
      'security/detect-non-literal-fs-filename': 0,
      'security/detect-non-literal-require': 0,
      'security/detect-object-injection': 0,
      'space-before-function-paren': 0,
      indent: 'off',
      'object-curly-spacing': 0,
      'one-var': 0,
      'dot-notation': 0,
      'comma-dangle': 0,
      semi: 0,
      'key-spacing': 0,
      'max-len': 0,
      strict: 0,
      quotes: 0,
      'no-unused-vars': 0,
      'no-undef': 0
    }
  }
];

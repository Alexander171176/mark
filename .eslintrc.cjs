const { defineConfig } = require('eslint-define-config');

module.exports = defineConfig({
    root: true,

    ignorePatterns: [
        'node_modules/',
        'public/',
        'vendor/',
        'storage/',
        'bootstrap/',
        'dist/',
        '.idea/',
        '.vscode/',
    ],

    overrides: [
        {
            files: ['**/*.js', '**/*.cjs', '**/*.vue'],
            parserOptions: {
                ecmaVersion: 'latest',
                sourceType: 'module',
            },
            rules: {
                'no-console': process.env.NODE_ENV === 'production' ? 'warn' : 'off',
                'no-debugger': process.env.NODE_ENV === 'production' ? 'warn' : 'off',
                'vue/multi-word-component-names': 'off',
                'no-undef': 'off',
                'no-throw-literal': 'off',
            },
        },
    ],
});

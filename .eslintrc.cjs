const { defineConfig } = require('eslint-define-config');

module.exports = defineConfig({
    root: true,

    env: {
        browser: true,
        node: true,
        es2022: true,
    },

    globals: {
        route: 'readonly',
    },

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

    extends: [
        'eslint:recommended',
        'plugin:vue/vue3-recommended',
    ],

    parser: 'vue-eslint-parser',

    parserOptions: {
        parser: '@babel/eslint-parser',
        ecmaVersion: 'latest',
        sourceType: 'module',
        requireConfigFile: false,
    },

    plugins: [
        'vue',
    ],

    overrides: [
        {
            files: ['**/*.vue'],
            rules: {
                'vue/multi-word-component-names': 'off',

                'vue/html-indent': 'off',
                'vue/max-attributes-per-line': 'off',
                'vue/singleline-html-element-content-newline': 'off',
                'vue/multiline-html-element-content-newline': 'off',
                'vue/html-closing-bracket-newline': 'off',
                'vue/html-self-closing': 'off',

                'vue/v-on-event-hyphenation': 'off',
                'vue/attribute-hyphenation': 'off',
                'vue/first-attribute-linebreak': 'off',
                'vue/attributes-order': 'off',

                'vue/no-side-effects-in-computed-properties': 'off',
                'vue/require-default-prop': 'off',
                //'vue/require-prop-types': 'off', // ← добавить
                'vue/prop-name-casing': 'off',
                'vue/no-v-html': 'off',

                'no-undef': 'off',
                'no-throw-literal': 'off',
            },
        },

        {
            files: ['**/*.js', '**/*.cjs'],
            rules: {
                'no-console': process.env.NODE_ENV === 'production' ? 'warn' : 'off',
                'no-debugger': process.env.NODE_ENV === 'production' ? 'warn' : 'off',
                'no-undef': 'off',
                'no-throw-literal': 'off',
            },
        },
    ],
});

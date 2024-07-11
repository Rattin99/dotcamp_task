const defaultConfig = require('@wordpress/scripts/config/webpack.config');

module.exports = {
    ...defaultConfig,
    entry: {
        'signup-list': './src/blocks/signup-list/index.js',
    },
    output: {
        ...defaultConfig.output,
        path: __dirname + '/build',
    },
};
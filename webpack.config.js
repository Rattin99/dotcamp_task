const defaultConfig = require('@wordpress/scripts/config/webpack.config');

module.exports = {
    ...defaultConfig,
    entry: {
        'signup-list': './src/Blocks/signup-list/index.js',
    },
    output: {
        ...defaultConfig.output,
        path: __dirname + '/build',
    },
    externals: {
        ...defaultConfig.externals,
        '@wordpress/element': 'wp.element',
        '@wordpress/server-side-render': 'wp.serverSideRender'
    }
};
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const path = require( 'path' );

module.exports = {
    ...defaultConfig,
    entry: {
        classic: path.resolve( __dirname, 'resources/js/classic.tsx' ),
        gutenberg: path.resolve( __dirname, 'resources/js/gutenberg.tsx' ),
    },
    output: {
        ...defaultConfig.output,
        path: path.resolve( __dirname, 'build' ),
    },
    resolve: {
        ...defaultConfig.resolve,
        alias: {
            ...( defaultConfig.resolve?.alias ?? {} ),
            '@uix': path.resolve( __dirname, 'resources/js/src' ),
        },
    },
};

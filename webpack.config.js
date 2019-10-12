const path = require( 'path' );
const config = {
	entry: {
		backend: ['./admin/js/chosen.js', './admin/js/select.js']
	},
	output: {
		filename: 'gravityforms-nutshell-integration-admin.js',
		path: path.resolve( __dirname, './admin/js' )
	},
	module: {
		rules: [
			{
				// Look for any .js files.
				test: /\.js$/,
				// Exclude the node_modules folder.
				exclude: /node_modules/,
				// Use babel loader to transpile the JS files.
				loader: 'babel-loader'
			}
		]
	}
}

module.exports = config;

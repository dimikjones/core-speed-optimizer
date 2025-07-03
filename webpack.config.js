const webpack = require( 'webpack' );
const path = require( 'path' );
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );

const config = {
	entry: {
		admin: [
			'./assets/source/js/admin/core-speed-optimizer-admin.js',
			'./assets/source/sass/admin/core-speed-optimizer-admin.scss'
		],
		front: [
			'./assets/source/js/front/core-speed-optimizer.js',
			'./assets/source/sass/front/core-speed-optimizer.scss'
		]
	},
	output: {
		path: path.resolve(
			__dirname,
			'assets'
		),
		filename: '[name].js'
	},
	module: {
		rules: [
			{
				test: /\.js$/,
				use: 'babel-loader',
				exclude: /node_modules/
			},
			{
				test: /\.scss$/,
				use: [
					MiniCssExtractPlugin.loader,
					'css-loader',
					'sass-loader'
				]
			}
		]
	},
	plugins: [
		new MiniCssExtractPlugin()
	]
};

module.exports = config;
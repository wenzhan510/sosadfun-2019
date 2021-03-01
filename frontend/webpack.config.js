const path = require('path');
const webpack = require("webpack");
const OptimizeCSSAssetsPlugin = require("optimize-css-assets-webpack-plugin");
const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;
const HtmlWebpackPlugin = require("html-webpack-plugin");
const CleanWebpackPlugin = require("clean-webpack-plugin");
const merge = require("webpack-merge");
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const FontAwesomeMinifyPlugin = require("font-awesome-minify-plugin");
const CopyWebpackPlugin = require('copy-webpack-plugin');

function commonConfig (devMode) {
  return {
    entry: {
      polyfill: 'babel-polyfill', // for ie8
      app: './src/index.tsx',
      // app: './src/test/index.tsx', // only for webpack test
      vendor: [
        'react',
        'react-dom',
        'react-router-dom',
      ],
    },
    output: {
      path: path.resolve(__dirname, 'dist'),
      publicPath: '/',
      filename: devMode ? '[name].bundle.js' : '[name].bundle.min.js',
      chunkFilename: devMode ? '[name].chunk.js' : '[name].chunk.min.js',
    },
    resolve: {
      extensions: ['.ts', '.tsx', '.js', '.json'],
      modules: ['node_modules'],
      alias: {
      }
    },
    module: {
      rules: [
        { test: '/\.html$/', use: [
          { loader: 'html-loader', options: {
            attrs: ['img:src'],
          }},
        ]},
        { test: /\.tsx?$/, use: [
          { loader: 'babel-loader', options: {
            exclude: 'node_modules',
          }},
          'ts-loader',
        ]},
        { test: /\.s?css$/, use: [
          devMode ? { loader: 'style-loader', options: {
            singleton: true,
          }} : MiniCssExtractPlugin.loader,
          { loader: 'css-loader', options: {
            minimize: true,
            sourceMap: devMode ? true : false,
          }},
          { loader: 'postcss-loader', options: {
            plugins: () => [ require('precss'), require('autoprefixer')],
          }},
          { loader: 'sass-loader' },
        ]},
        { test: /\.(png|jpg|gif|svg|eot|ttf|woff|woff2)$/, use: [
          { loader: 'url-loader', options: {
            name: "[name]-[hash:5].min.[ext]",
            limit: 8192,
          }},
        ]},
      ]
    },
    optimization: {
      runtimeChunk: 'single',
      splitChunks: {
        chunks: 'async',
        minSize: 30000,
        maxSize: 0,
        minChunks: 1,
        maxAsyncRequests: 5,
        maxInitialRequests: 3,
        automaticNameDelimiter: '~',
        name: true,
        cacheGroups: {
          vendors: {
            test: /\/node_modules\//,
            priority: -10,
            chunks: 'initial',
          },
          'react-vendor': {
            test: /react/,
            priority: 1,
            chunks: 'initial',
          },
          default: {
            minChunks: 2,
            priority: -20,
            reuseExistingChunk: true,
          },
        },
      },
    },
    plugins: [
      new HtmlWebpackPlugin({
        filename: "index.html",
        template: "index.html",
        title: "Caching",
        minify: {
          collapseWhitespace: true,
          removeComments: true,
        },
      }),

      new CopyWebpackPlugin([
        {from:'assets', to:'dist/assets'}
      ])
    ],
  };
}

const devConfig = {
  mode: 'development',
  devtool: 'source-map',
  module: {
    rules: [
      { enforce: 'pre', test: /\.js$/, loader: 'source-map-loader' }, 
    ],
  },
  devServer: {
    contentBase: path.join(__dirname, 'dist'),
    compress: true,
    port: 2333,
    open: true,
    hot: true,
    host: '0.0.0.0',
    headers: {
      "Access-Control-Allow-Origin": "*",
      "Access-Control-Allow-Methods": "GET, POST, PUT, DELETE, PATCH, OPTIONS",
      "Access-Control-Allow-Headers": "X-Requested-With, content-type, Authorization",
    },
    overlay: true,
    historyApiFallback: true, 
  },
  plugins: [
    new webpack.HotModuleReplacementPlugin(),
    new webpack.NamedModulesPlugin(), // also for hot updates
  ],
};

const prodConfig = {
  mode: 'production',
  plugins: [
    new BundleAnalyzerPlugin(),
    new MiniCssExtractPlugin({
      filename: '[name].min.css',
      chunkFilename: '[id].min.css',
    }),
    new OptimizeCSSAssetsPlugin({}),
    new FontAwesomeMinifyPlugin(),
    new CleanWebpackPlugin(["dist"], {
      root: path.resolve(__dirname),
      verbose: true,
    }),
  ]
};

module.exports = (env, argv) => {
  console.log('---', env || argv.mode, '---');
  const devMode = argv.mode === 'development' || env !== 'production';
  const config = devMode ? devConfig : prodConfig;
  return merge(commonConfig(devMode), config);
};

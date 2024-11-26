const path = require('path');
const CopyPlugin = require('copy-webpack-plugin')


module.exports = {
    entry: {
        app: path.resolve(__dirname, './assets/app.js'),
    },
    output: {
        filename: '[name].js',
        path: path.resolve(__dirname, './public/assets'),
    },
    module: {
        rules: [
            { 
                test: /\.scss?$/, 
                exclude: /node_modules/, 
                // loader: ["style-loader", "css-loader", "sass-loader"] },
                use: [
                    // Creates `style` nodes from JS strings
                    "style-loader",
                    // Translates CSS into CommonJS
                    "css-loader",
                    // Compiles Sass to CSS
                    "sass-loader",
                ],
            },
            { 
                test: /\.css?$/, 
                //exclude: /node_modules/, 
                // loader: ["style-loader", "css-loader", "sass-loader"] },
                use: [
                    // Creates `style` nodes from JS strings
                    "style-loader",
                    // Translates CSS into CommonJS
                    "css-loader",
                ],
            },
            // {
            //     test: /\.html$/i,
            //     use: ["html-loader"],
            // },
            // {
            //     test: /\.txt$/i,
            //     use: 'raw-loader',
            // },
            // {
            //     test: /\.(png|jpg|gif)$/i,
            //     type: 'asset/resource',
            // },
        ]
    },
    plugins: [
        new CopyPlugin({
            patterns: [
                { from: "./assets/styles/enquete.css", to: "./" },
                // { from: "other", to: "public" },
            ],
        }),
    ],
};
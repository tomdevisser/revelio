const path = require("path");
const defaultConfig = require("@wordpress/scripts/config/webpack.config.js");

module.exports = {
  ...defaultConfig,
  entry: {
    "block-editor": path.resolve(__dirname, "source/js", "block-editor.js"),
    "classic-editor": path.resolve(__dirname, "source/js", "classic-editor.js"),
  },
  output: {
    path: path.resolve(__dirname, "build"),
  },
};

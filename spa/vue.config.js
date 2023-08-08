const { defineConfig } = require('@vue/cli-service')

module.exports = defineConfig({
  transpileDependencies: true,
  chainWebpack: config => {
    config.plugins.delete('fork-ts-checker') // Если используете TypeScript
  },
  configureWebpack: {
    plugins: [],
  },
  devServer: {
    hot: true, // Включить горячую перезагрузку (hot reload)
  },
})
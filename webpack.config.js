var Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
  Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
  .setOutputPath('public/build/')
  .setPublicPath('/build')
  .addEntry('app', './assets/js/app.js')
  .addEntry('admin', './assets/admin/js/admin.js')
  .addStyleEntry('api-doc', './assets/css/api-doc.scss')
  .copyFiles({
      from: './assets/images',
      to: 'images/[path][name].[ext]'
    }
  )
  .copyFiles({
      from: './assets/fonts',
      to: 'fonts/[name].[ext]'
    }
  )
  .splitEntryChunks()
  .enableSingleRuntimeChunk()
  .cleanupOutputBeforeBuild()
  .enableVueLoader(() => {}, { runtimeCompilerBuild: false })
  .enableSassLoader()
  .enablePostCssLoader()
  .enableBuildNotifications()
  .enableSourceMaps(!Encore.isProduction())
  .configureBabelPresetEnv((config) => {
    config.useBuiltIns = 'usage';
    config.corejs = 3;
  })
;

module.exports = Encore.getWebpackConfig();

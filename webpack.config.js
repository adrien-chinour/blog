const Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')

    .enableStimulusBridge('./assets/controllers.json')

    .addEntry('app', './assets/entrypoint/app.js')

    .splitEntryChunks()

    .enableReactPreset()

    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())

    .configureBabelPresetEnv((config) => {
        config.targets = '> .5%, not dead, not op_mini all, last 2 versions';
    })

    .enablePostCssLoader()
    .enableSassLoader()
;

module.exports = Encore.getWebpackConfig();

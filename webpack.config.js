const Encore = require('@symfony/webpack-encore');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // 1. Répertoires
    .setOutputPath('public/build/')
    .setPublicPath('/build')

    // 2. Entry principal (toujours chargé partout)
    .addEntry('app', './assets/app.ts')

    // Split chunks
    .splitEntryChunks()
    .enableSingleRuntimeChunk()

    // Copie des images et polices
    .copyFiles({
        from: './assets/images',
        to: 'images/[path][name].[hash:8].[ext]',
        pattern: /\.(png|jpg|jpeg|gif|ico|svg|webp)$/
    })

    // Copie des polices Font Awesome depuis node_modules
    .copyFiles({
        from: './node_modules/@fortawesome/fontawesome-free/webfonts',
        to: 'fonts/[name].[hash:8].[ext]',
        pattern: /\.(woff|woff2|ttf|eot|otf|svg)$/
    })

    // Features
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())

    // Loaders
    .enableSassLoader((options) => {
        options.sassOptions = {
            silenceDeprecations: ['legacy-js-api'],
        };
    })
    .enableTypeScriptLoader()

    // jQuery
    .autoProvidejQuery()
    ;

module.exports = Encore.getWebpackConfig();
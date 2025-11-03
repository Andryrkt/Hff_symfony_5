const Encore = require('@symfony/webpack-encore');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    
    // Entrées
    .addEntry('app', './assets/app.ts')
    .addEntry('login', './assets/js/login/login.ts')
    .addEntry('accueil', './assets/js/accueil.js')

    // Split chunks
    .splitEntryChunks()
    .enableSingleRuntimeChunk()

    // Copie des images et polices
    .copyFiles({
        from: './assets/images',
        to: 'images/[path][name].[hash:8].[ext]',
        pattern: /\.(png|jpg|jpeg|gif|ico|svg|webp)$/
    })
    .copyFiles({
        from: './assets/fonts',
        to: 'fonts/[path][name].[hash:8].[ext]',
        pattern: /\.(woff|woff2|ttf|eot|otf)$/
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

    // Babel - NOTE: This is commented out because a .babelrc file is present.
    // .configureBabel((config) => {
    //     config.plugins.push('@babel/plugin-proposal-class-properties');
    // })
    // .configureBabelPresetEnv((config) => {
    //     config.useBuiltIns = 'usage';
    //     config.corejs = 3;
    // })

    // Loaders
    .enableSassLoader()
    .enableTypeScriptLoader()

    // jQuery
    .autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
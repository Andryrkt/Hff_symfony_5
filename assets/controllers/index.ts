// assets/controllers/index.js

// Ce fichier est requis par enableStimulusBridge() dans webpack.config.js
// Il charge automatiquement tous les contrôleurs Stimulus

const context = require.context('./', true, /_controller\.(js|ts)$/);

// Export pour Stimulus Bridge
export default context.keys().map(key => context(key));
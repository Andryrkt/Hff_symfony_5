import { Controller } from '@hotwired/stimulus';
import { initAgenceServiceCasierHandlers } from '../../../../../js/utils/AgenceServiceCasierManager';

/**
 * Ce contrôleur a pour seul but d'appeler des fonctions d'initialisation
 * spécifiques à une page, qui ne sont pas nécessaires globalement.
 */
export default class extends Controller {
    connect() {
        // Le gestionnaire Agence/Service/Casier est nécessaire sur cette page.
        // Il scannera le document à la recherche d'éléments correspondants
        // (ex: #agence-service-destinataire) pour les initialiser.
        initAgenceServiceCasierHandlers();
    }
}

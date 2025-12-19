import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ['idMateriel', 'numParc', 'numSerie'];

    declare idMaterielTarget: HTMLInputElement;
    declare numParcTarget: HTMLInputElement;
    declare numSerieTarget: HTMLInputElement;

    connect() {
        // console.log('CasierFirstFormController connected');
        this.validateFields();
    }

    validateFields() {
        const idMateriel = this.idMaterielTarget.value.trim();
        const numParc = this.numParcTarget.value.trim();
        const numSerie = this.numSerieTarget.value.trim();

        // Vérifier si au moins un champ est rempli
        const atLeastOneFilled = idMateriel !== '' || numParc !== '' || numSerie !== '';

        if (atLeastOneFilled) {
            // Si au moins un champ est rempli, retirer l'obligation des autres
            this.idMaterielTarget.removeAttribute('required');
            this.numParcTarget.removeAttribute('required');
            this.numSerieTarget.removeAttribute('required');

            // Retirer les messages d'erreur personnalisés
            this.idMaterielTarget.setCustomValidity('');
            this.numParcTarget.setCustomValidity('');
            this.numSerieTarget.setCustomValidity('');
        } else {
            // Si aucun champ n'est rempli, tous deviennent obligatoires
            this.idMaterielTarget.setAttribute('required', 'required');
            this.numParcTarget.setAttribute('required', 'required');
            this.numSerieTarget.setAttribute('required', 'required');

            // Définir un message d'erreur personnalisé
            const message = 'Au moins un des trois champs (Id Materiel, N° Parc, N° Serie) doit être rempli';
            this.idMaterielTarget.setCustomValidity(message);
            this.numParcTarget.setCustomValidity(message);
            this.numSerieTarget.setCustomValidity(message);
        }
    }
}

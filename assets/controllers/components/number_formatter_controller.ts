import { Controller } from "@hotwired/stimulus";
import numeral from "numeral";

export default class extends Controller {
    static targets = ["input"];

    declare inputTarget: HTMLInputElement;

    connect() {
        this.inputTarget.addEventListener("input", this.formatNumber.bind(this));
        // Appliquer le formatage au chargement initial si le champ est pré-rempli
        this.formatNumber();
    }

    disconnect() {
        this.inputTarget.removeEventListener("input", this.formatNumber.bind(this));
    }

    formatNumber() {
        let value = this.inputTarget.value;

        // Supprime tous les caractères non numériques, sauf la virgule et le point.
        // Permet également un seul point ou une seule virgule pour les décimales.
        value = value.replace(/[^\d.,]/g, '');

        // Remplace la virgule par un point pour uniformiser le traitement interne
        value = value.replace(",", ".");

        // Si la valeur est vide après nettoyage, on ne fait rien pour éviter 'NaN' ou '0 '
        if (value.trim() === '') {
            this.inputTarget.value = '';
            return;
        }

        // S'assurer qu'il n'y a qu'un seul point décimal
        const parts = value.split('.');
        if (parts.length > 2) {
            // Reconstruit la valeur avec seulement le premier point et fusionne les autres parties.
            value = parts.shift() + '.' + parts.join('');
        }

        // Formate la partie entière avec un espace pour les milliers
        // En utilisant numeral pour formater, puis en ajustant le séparateur décimal si nécessaire
        let formattedValue;
        if (value.includes(".")) {
            let [integerPart, decimalPart] = value.split(".");
            // Formatage de la partie entière uniquement
            integerPart = numeral(integerPart).format('0,0').replace(/,/g, ' ');

            // Recombine avec la partie décimale
            formattedValue = integerPart + ',' + decimalPart;
        } else {
            // Formate la valeur entière
            formattedValue = numeral(value).format('0,0').replace(/,/g, ' ');
        }
        
        // Met à jour la valeur de l'input
        this.inputTarget.value = formattedValue;
    }
}

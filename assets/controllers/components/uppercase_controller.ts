import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["input"];

    declare inputTarget: HTMLInputElement;

    connect() {
        this.inputTarget.addEventListener("input", this.toUppercase.bind(this));
        // Force la valeur initiale en majuscules au cas où le champ est pré-rempli
        this.toUppercase(); 
    }

    disconnect() {
        this.inputTarget.removeEventListener("input", this.toUppercase.bind(this));
    }

    toUppercase() {
        this.inputTarget.value = this.inputTarget.value.toUpperCase();
    }
}

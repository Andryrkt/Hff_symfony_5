import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
  static targets = ["agenceServiceSection"];

  // Declare properties for targets
  agenceServiceSectionTarget!: HTMLElement;

  connect() {
    console.log("DOM Form controller connected");
    // Autres initialisations du formulaire principal
    this.initializeForm();
  }

  initializeForm() {
    console.log("Initializing main form...");
    // Logique d'initialisation du formulaire principal
    // sans la gestion agence/service qui est déléguée à agence-selector
  }

  // Autres méthodes pour la gestion du formulaire principal
  validateForm() {
    // Logique de validation
  }

  onSubmit(event: Event) {
    // Logique de soumission
  }
}
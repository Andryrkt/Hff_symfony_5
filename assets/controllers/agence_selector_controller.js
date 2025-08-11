import { Controller } from "@hotwired/stimulus";
import { FetchManager } from "../js/utils/FetchManager";

export default class extends Controller {
  static targets = ["agence", "service"];

  connect() {
    console.log("Agence Selector controller connected");
    this.initializeServiceField();
  }

  initializeServiceField() {
    if (!this.hasAgenceTarget || !this.hasServiceTarget) {
      console.warn("Required targets not found");
      return;
    }

    // Désactiver le champ service initialement
    this.serviceTarget.disabled = true;
    this.serviceTarget.innerHTML =
      '<option value="">-- Sélectionnez d\'abord une agence --</option>';
  }

  async onAgenceChange(event) {
    const agenceId = event.target.value;
    const fetchManager = new FetchManager();

    if (!agenceId) {
      this.resetServiceField();
      return;
    }

    try {
      this.showLoadingState();
      const services = await fetchManager.get(
        `/api/agences/${agenceId}/services`
      );
      this.updateServiceOptions(services);
    } catch (error) {
      console.error("Error loading services:", error);
      this.showErrorState();
    }
  }

  resetServiceField() {
    this.serviceTarget.innerHTML =
      '<option value="">-- Sélectionnez d\'abord une agence --</option>';
    this.serviceTarget.disabled = true;
  }

  showLoadingState() {
    this.serviceTarget.disabled = true;
    this.serviceTarget.innerHTML =
      '<option value="">Chargement des services...</option>';
  }

  updateServiceOptions(services) {
    // Supporte JSON-LD (hydra:member) et un tableau JSON simple
    const list = Array.isArray(services)
      ? services
      : services && services["hydra:member"]
      ? services["hydra:member"]
      : [];

    let options = '<option value="">-- Choisir un service --</option>';
    list.forEach((service) => {
      const label = `${service.code} - ${service.nom ?? service.name ?? ""}`;
      options += `<option value="${service.id}">${label}</option>`;
    });
    this.serviceTarget.innerHTML = options;
    this.serviceTarget.disabled = false;
  }

  showErrorState() {
    this.serviceTarget.innerHTML =
      '<option value="">Erreur de chargement</option>';
    this.serviceTarget.disabled = true;
  }
}

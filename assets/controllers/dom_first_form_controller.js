import { Controller } from "stimulus";
import $ from "jquery";

export default class extends Controller {
  static targets = [
    "salarieType",
    "nomGroup",
    "prenomGroup",
    "cinGroup",
    "matriculeGroup",
    "matriculeNomSelect",
    "matriculeInput",
    "sousTypeDocument",
    "categorieGroup",
  ];

  connect() {
    this.initSelect2();
    this.toggleFields();
  }

  // Gestion des champs selon le type de salarié
  toggleFields() {
    const isTemporary = this.salarieTypeTarget.value === "TEMPORAIRE";

    const temporaryGroups = [this.nomGroupTarget, this.prenomGroupTarget, this.cinGroupTarget];
    const permanentGroups = [this.matriculeGroupTarget];

    temporaryGroups.forEach((groupEl) => {
      groupEl.style.display = isTemporary ? "block" : "none";
      const inputEl = groupEl.querySelector('input, select, textarea');
      if (inputEl) {
        inputEl.disabled = !isTemporary;
      }
    });

    permanentGroups.forEach((groupEl) => {
      groupEl.style.display = isTemporary ? "none" : "block";
      // Assuming matriculeNomSelectTarget and matriculeInputTarget are direct children or easily queryable within matriculeGroupTarget
      // If they are Stimulus targets, we can directly access them
      if (this.hasMatriculeNomSelectTarget) {
        this.matriculeNomSelectTarget.disabled = isTemporary;
      }
      if (this.hasMatriculeInputTarget) {
        this.matriculeInputTarget.disabled = isTemporary;
      }
    });
  }

  // Initialisation de Select2 pour le matricule
  initSelect2() {
    $(this.matriculeNomSelectTarget)
      .select2({
        width: "100%",
        placeholder: "-- choisir un personnel --",
      })
      .on("select2:select", (e) => {
        this.matriculeInputTarget.value = e.params.data.text.match(/^\w+/)[0]; // Extrait le matricule
      });
  }

  // Gestion dynamique de la catégorie
  updateCategorie() {
    const typeDoc = this.sousTypeDocumentTarget.value;
    const agence = this.element.querySelector(
      '[name*="[agenceEmetteur]"]'
    ).value;

    fetch(`/dom/categories?typeDoc=${typeDoc}&agence=${agence}`)
      .then((response) => response.json())
      .then((data) => this.updateCategorieSelect(data))
      .catch(console.error);
  }

  updateCategorieSelect(categories) {
    const select = this.categorieGroupTarget.querySelector("select");
    select.innerHTML = "";

    categories.forEach((cat) => {
      const option = document.createElement("option");
      option.value = cat.id;
      option.textContent = cat.description;
      select.appendChild(option);
    });

    this.categorieGroupTarget.style.display = categories.length
      ? "block"
      : "none";
  }
}

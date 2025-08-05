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

    [this.nomGroupTarget, this.prenomGroupTarget, this.cinGroupTarget].forEach(
      (el) => {
        el.style.display = isTemporary ? "block" : "none";
      }
    );

    this.matriculeGroupTarget.style.display = isTemporary ? "none" : "block";
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

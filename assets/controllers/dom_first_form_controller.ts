import { Controller } from "@hotwired/stimulus";
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

  // Declare properties for targets
  salarieTypeTarget!: HTMLSelectElement;
  nomGroupTarget!: HTMLElement;
  prenomGroupTarget!: HTMLElement;
  cinGroupTarget!: HTMLElement;
  matriculeGroupTarget!: HTMLElement;
  matriculeNomSelectTarget!: HTMLSelectElement;
  matriculeInputTarget!: HTMLInputElement;
  sousTypeDocumentTarget!: HTMLSelectElement;
  categorieGroupTarget!: HTMLElement;
  hasMatriculeNomSelectTarget!: boolean;
  hasMatriculeInputTarget!: boolean;
  hasCategorieGroupTarget!: boolean;


  connect() {
    this.initSelect2();
    this.toggleFields();

    // Ajouter un listener pour debug
    this.element.addEventListener("submit", this.onSubmit.bind(this));
  }

  // Debug pour voir si le formulaire se soumet
  onSubmit(event: Event) {
    console.log("Form submitting...", event);
    console.log("Form data:", new FormData(event.target as HTMLFormElement));

    // Vérifier les champs requis avant soumission
    const requiredFields = this.element.querySelectorAll(
      "[required]:not([disabled])"
    );
    let hasErrors = false;

    requiredFields.forEach((field) => {
      if (!(field as HTMLInputElement).value.trim()) {
        console.error("Required field empty:", (field as HTMLInputElement).name || field.id, field);
        hasErrors = true;
      }
    });

    if (hasErrors) {
      console.log("Form has validation errors");
    }
  }

  // Gestion des champs selon le type de salarié
  toggleFields() {
    const isTemporary = this.salarieTypeTarget.value === "TEMPORAIRE";

    // Gérer les champs temporaires
    const temporaryGroups = [
      this.nomGroupTarget,
      this.prenomGroupTarget,
      this.cinGroupTarget,
    ];

    temporaryGroups.forEach((groupEl) => {
      const inputEl = groupEl.querySelector("input, select, textarea") as HTMLInputElement;

      if (isTemporary) {
        // Montrer et activer les champs temporaires
        groupEl.style.display = "block";
        if (inputEl) {
          inputEl.disabled = false;
          inputEl.required = true; // Rendre requis si nécessaire
        }
      } else {
        // Cacher et vider les champs temporaires (mais ne pas les désactiver)
        groupEl.style.display = "none";
        if (inputEl) {
          inputEl.value = ""; // Vider la valeur
          inputEl.required = false; // Retirer l'obligation
          // NE PAS utiliser disabled = true
        }
      }
    });

    // Gérer les champs permanents
    const matriculeGroup = this.matriculeGroupTarget;

    if (isTemporary) {
      // Cacher les champs permanents
      matriculeGroup.style.display = "none";

      if (this.hasMatriculeNomSelectTarget) {
        // Vider et désélectionner
        $(this.matriculeNomSelectTarget).val('').trigger("change");
        this.matriculeNomSelectTarget.required = false;
      }

      if (this.hasMatriculeInputTarget) {
        this.matriculeInputTarget.value = "";
        this.matriculeInputTarget.required = false;
      }
    } else {
      // Montrer les champs permanents
      matriculeGroup.style.display = "block";

      if (this.hasMatriculeNomSelectTarget) {
        this.matriculeNomSelectTarget.required = true;
      }

      if (this.hasMatriculeInputTarget) {
        this.matriculeInputTarget.required = true;
      }
    }
  }

  // Initialisation de Select2 pour le matricule
  initSelect2() {
    if (!this.hasMatriculeNomSelectTarget) return;

    $(this.matriculeNomSelectTarget)
      .select2({
        width: "100%",
        placeholder: "-- choisir un personnel --",
        allowClear: true,
      })
      .on("select2:select", (e: any) => {
        // Améliorer l'extraction du matricule
        const text = e.params.data.text;
        const matriculeMatch = text.match(/^(\w+)/);
        if (matriculeMatch && this.hasMatriculeInputTarget) {
          this.matriculeInputTarget.value = matriculeMatch[1];
        }
      })
      .on("select2:unselect", () => {
        if (this.hasMatriculeInputTarget) {
          this.matriculeInputTarget.value = "";
        }
      });
  }

  // Gestion dynamique de la catégorie
  async updateCategorie() {
    try {
      const typeDoc = this.sousTypeDocumentTarget.value;
      const agenceElement = this.element.querySelector(
        '[name*="[agenceEmetteur]"]'
      ) as HTMLInputElement;

      if (!agenceElement || !agenceElement.value || !typeDoc) {
        console.log("Missing required data for category update");
        this.hideCategorieSelect();
        return;
      }

      const agence = agenceElement.value;

      console.log("Fetching categories for:", { typeDoc, agence });

      const response = await fetch(
        `/dom/categories?typeDoc=${encodeURIComponent(
          typeDoc
        )}&agence=${encodeURIComponent(agence)}`
      );

      if (!response.ok) {
        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
      }

      const data = await response.json();

      if (data.error) {
        throw new Error(data.error);
      }

      this.updateCategorieSelect(data);
    } catch (error) {
      console.error("Error updating categories:", error);
      this.hideCategorieSelect();
    }
  }

  updateCategorieSelect(categories: any[]) {
    if (!this.hasCategorieGroupTarget) return;

    const select = this.categorieGroupTarget.querySelector("select");
    if (!select) return;

    // Vider les options existantes
    select.innerHTML = "";

    // Ajouter une option par défaut
    const defaultOption = document.createElement("option");
    defaultOption.value = "";
    defaultOption.textContent = "-- Sélectionner une catégorie --";
    select.appendChild(defaultOption);

    // Ajouter les catégories
    categories.forEach((cat: any) => {
      const option = document.createElement("option");
      option.value = cat.id;
      option.textContent = cat.description;
      select.appendChild(option);
    });

    // Montrer le groupe si il y a des catégories
    if (categories.length > 0) {
      this.categorieGroupTarget.style.display = "block";
      select.required = true;
    } else {
      this.hideCategorieSelect();
    }
  }

  hideCategorieSelect() {
    if (this.hasCategorieGroupTarget) {
      this.categorieGroupTarget.style.display = "none";
      const select = this.categorieGroupTarget.querySelector("select");
      if (select) {
        select.required = false;
        select.value = "";
      }
    }
  }
}

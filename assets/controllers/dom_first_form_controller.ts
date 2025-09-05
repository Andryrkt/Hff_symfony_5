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
    "categorie",
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
  categorieTarget!: HTMLSelectElement;
  hasMatriculeNomSelectTarget!: boolean;
  hasMatriculeInputTarget!: boolean;
  hasCategorieTarget!: boolean;


  connect() {
    this.initSelect2();
    this.toggleFields();

    // Ajouter un listener pour debug
    this.element.addEventListener("submit", this.onSubmit.bind(this));
    this.updateCategorie();
    this.toggleCategorie();
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

  // Gestion dynamique de la catégorie avec cache côté client
  private categoryCache = new Map<string, any[]>();
  private debounceTimer: number | null = null;

  async updateCategorie() {
    console.log("updateCategorie called");
    // Debounce pour éviter les appels multiples
    if (this.debounceTimer) {
      clearTimeout(this.debounceTimer);
    }

    this.debounceTimer = window.setTimeout(async () => {
      try {
        const typeDoc = this.sousTypeDocumentTarget.value;
        const agenceElement = this.element.querySelector(
          '[name*="agenceEmetteur"]'
        ) as HTMLInputElement;
        console.log("typeDoc:", typeDoc);
        console.log("agenceElement:", agenceElement);

        if (!agenceElement || !agenceElement.value || !typeDoc) {
          this.hideCategorieSelect();
          return;
        }

        const agence = agenceElement.value;
        const cacheKey = `${typeDoc}_${agence}`;
        console.log("agence:", agence);

        // Vérifier le cache côté client
        if (this.categoryCache.has(cacheKey)) {
          this.updateCategorieSelect(this.categoryCache.get(cacheKey)!);
          return;
        }

        const url = `${window.location.protocol}//${window.location.host}/dom/categories?typeDoc=${encodeURIComponent(
          typeDoc
        )}&agence=${encodeURIComponent(agence)}`;

        console.log("url:", url);
        const response = await fetch(url, {
          method: 'GET',
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
          },
          credentials: 'same-origin'
        });

        if (!response.ok) {
          throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const data = await response.json();
        console.log("data:", data);

        if (data.error) {
          throw new Error(data.error);
        }

        // Mettre en cache les résultats
        this.categoryCache.set(cacheKey, data);
        this.updateCategorieSelect(data);
      } catch (error) {
        console.error("Error updating categories:", error);
        this.hideCategorieSelect();
      }
    }, 300); // Debounce de 300ms
  }

  updateCategorieSelect(categories: any[]) {
    if (!this.hasCategorieTarget) {
      return;
    }

    const select = this.categorieTarget;

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
      select.disabled = false;
    } else {
      this.hideCategorieSelect();
    }
  }

  hideCategorieSelect() {
    if (this.hasCategorieTarget) {
      const select = this.categorieTarget;
      select.required = false;
      select.value = "";
      select.disabled = true;
    }
  }

  toggleCategorie() {
    if (this.sousTypeDocumentTarget.selectedOptions.length > 0) {
      const selectedOption = this.sousTypeDocumentTarget.selectedOptions[0];
      if (selectedOption.text === 'MISSION') {
        (this.categorieTarget.closest('.row') as HTMLElement).style.display = 'block';
      } else {
        (this.categorieTarget.closest('.row') as HTMLElement).style.display = 'none';
      }
    } else {
      (this.categorieTarget.closest('.row') as HTMLElement).style.display = 'none';
    }
  }
}

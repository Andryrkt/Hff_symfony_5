import { Controller } from "@hotwired/stimulus";
import { FetchManager } from "../../../../../js/utils/FetchManager";
import { AutoComplete } from "../../../../../js/components/AutoComplete";

export default class extends Controller {
    connect() {
        const fetchManager = new FetchManager();
        let lastSelectedItem = null;

        const idMaterielInput = document.querySelector("#dit_form_idMateriel") as HTMLInputElement;
        const numParcInput = document.querySelector("#dit_form_numParc") as HTMLInputElement;
        const numSerieInput = document.querySelector("#dit_form_numSerie") as HTMLInputElement;
        const containerInfoMateriel = document.querySelector("#containerInfoMateriel") as HTMLElement;

        if (!idMaterielInput || !numParcInput || !numSerieInput || !containerInfoMateriel) {
            console.error("DitFormController: One or more required elements not found.");
            return;
        }

        const CACHE_KEY = "hff_materiels_cache";
        const CACHE_EXPIRY = 3600 * 1000; // 1 heure

        let materielsCache: any[] | null = null;
        let materielsPromise: Promise<any[]> | null = null;

        async function fetchMateriels() {
            // 1. Vérifier le cache en mémoire vive (le plus rapide)
            if (materielsCache) return materielsCache;

            // 2. Vérifier le localStorage (persistance entre pages/sessions)
            const cached = localStorage.getItem(CACHE_KEY);
            if (cached) {
                const { data, timestamp } = JSON.parse(cached);
                if (Date.now() - timestamp < CACHE_EXPIRY) {
                    materielsCache = data;
                    return data;
                }
            }

            // 3. Mutualiser les requêtes réseau (si plusieurs appels simultanés)
            if (!materielsPromise) {
                materielsPromise = fetchManager.get('ajax/fetch-materiel').then(data => {
                    materielsCache = data;
                    localStorage.setItem(CACHE_KEY, JSON.stringify({
                        data: data,
                        timestamp: Date.now()
                    }));
                    return data;
                }).finally(() => {
                    materielsPromise = null;
                });
            }

            return materielsPromise;
        }

        function displayMateriel(item) {
            return `Id: ${item.num_matricule} - Parc: ${item.num_parc} - S/N: ${item.num_serie}`;
        }

        // Met à jour les champs et la fiche
        function onSelectMateriels(item) {
            lastSelectedItem = item;

            idMaterielInput.value = item.num_matricule;
            numParcInput.value = item.num_parc;
            numSerieInput.value = item.num_serie;

            createMaterielInfoDisplay(containerInfoMateriel, item);
        }

        // Vérifie si la valeur tapée correspond à un item connu
        async function validateInput(input, keyToCompare) {
            const data = await fetchMateriels();
            const match = data.find((item) => item[keyToCompare] === input.value);

            if (!match) {
                containerInfoMateriel.innerHTML = `
      <div class="text-danger fw-bold">Aucun matériel trouvé pour "${input.value}". Veuillez choisir un élément dans la liste.</div>
    `;
                lastSelectedItem = null;
            }
        }

        // Écouteurs de perte de focus pour chaque champ
        idMaterielInput.addEventListener("blur", () =>
            validateInput(idMaterielInput, "num_matricule")
        );
        numParcInput.addEventListener("blur", () =>
            validateInput(numParcInput, "num_parc")
        );
        numSerieInput.addEventListener("blur", () =>
            validateInput(numSerieInput, "num_serie")
        );

        //Activation sur le champ Id Matériel
        new AutoComplete({
            inputElement: idMaterielInput,
            suggestionContainer: document.querySelector("#suggestion-idMateriel"),
            loaderElement: document.querySelector("#loader-idMateriel"), // Ajout du loader
            debounceDelay: 300, // Délai en ms
            fetchDataCallback: fetchMateriels,
            displayItemCallback: displayMateriel,
            onSelectCallback: onSelectMateriels,
            itemToStringCallback: (item) =>
                `${item.num_matricule} - ${item.num_parc} - ${item.num_serie}`,
        });

        //Activation sur le champ numSerie
        new AutoComplete({
            inputElement: numSerieInput,
            suggestionContainer: document.querySelector("#suggestion-numSerie"),
            loaderElement: document.querySelector("#loader-numSerie"), // Ajout du loader
            debounceDelay: 300, // Délai en ms
            fetchDataCallback: fetchMateriels,
            displayItemCallback: displayMateriel,
            onSelectCallback: onSelectMateriels,
            itemToStringCallback: (item) =>
                `${item.num_matricule} - ${item.num_parc} - ${item.num_serie}`,
        });

        //Activation sur le champ numParc
        new AutoComplete({
            inputElement: numParcInput,
            suggestionContainer: document.querySelector("#suggestion-numParc"),
            loaderElement: document.querySelector("#loader-numParc"), // Ajout du loader
            debounceDelay: 300, // Délai en ms
            fetchDataCallback: fetchMateriels,
            displayItemCallback: displayMateriel,
            onSelectCallback: onSelectMateriels,
            itemToStringCallback: (item) =>
                `${item.num_matricule} - ${item.num_parc} - ${item.num_serie}`,
        });

        // --- Logique Interne / Externe ---
        const interneExterneInput = document.querySelector("#dit_form_interneExterne") as HTMLSelectElement;
        const nomClientInput = document.querySelector("#dit_form_nomClient") as HTMLInputElement;
        const numClientInput = document.querySelector("#dit_form_numeroClient") as HTMLInputElement;
        const numTelInput = document.querySelector("#dit_form_numeroTel") as HTMLInputElement;
        const clientSousContratInput = document.querySelector("#dit_form_clientSousContrat") as HTMLSelectElement;
        const mailClientInput = document.querySelector("#dit_form_mailClient") as HTMLInputElement;
        const demandeDevisInput = document.querySelector("#dit_form_demandeDevis") as HTMLSelectElement;
        const agenceDebiteurInput = document.querySelector("#dit_form_debiteur_agence") as HTMLSelectElement;
        const serviceDebiteurInput = document.querySelector("#dit_form_debiteur_service") as HTMLSelectElement;

        const toggleInterneExterne = () => {
            const isInterne = interneExterneInput.value === "INTERNE";
            const isExterne = interneExterneInput.value === "EXTERNE";

            const infoData = interneExterneInput.dataset.informations;
            const parsedData = infoData ? JSON.parse(infoData) : { agenceId: "", serviceId: "" };

            // Champs Client
            [nomClientInput, numClientInput, numTelInput, mailClientInput].forEach(input => {
                if (input) {
                    input.disabled = isInterne;
                    if (isExterne) input.setAttribute("required", "true");
                    else input.removeAttribute("required");
                }
            });

            if (clientSousContratInput) clientSousContratInput.disabled = isInterne;

            // Demande de devis
            if (demandeDevisInput) {
                demandeDevisInput.disabled = isInterne;
                demandeDevisInput.value = isExterne ? "OUI" : "NON";
            }

            // Agence et Service Débiteur
            if (agenceDebiteurInput && serviceDebiteurInput) {
                agenceDebiteurInput.disabled = isExterne;
                serviceDebiteurInput.disabled = isExterne;

                if (isInterne) {
                    agenceDebiteurInput.value = parsedData.agenceId;
                    serviceDebiteurInput.value = parsedData.serviceId;
                    // Forcer le dispatch d'un événement change pour TomSelect ou autres managers
                    agenceDebiteurInput.dispatchEvent(new Event('change'));
                } else if (isExterne) {
                    agenceDebiteurInput.value = "";
                    serviceDebiteurInput.value = "";
                    agenceDebiteurInput.dispatchEvent(new Event('change'));
                }
            }
        };

        if (interneExterneInput) {
            interneExterneInput.addEventListener("change", toggleInterneExterne);
            // Initialisation au chargement
            toggleInterneExterne();
        }

        function createMaterielInfoDisplay(container, data) {
            if (!container) {
                console.error("Container not found.");
                return;
            }

            if (!hasValidData(data)) {
                showNoDataMessage(container);
                return;
            }

            const fields = getMaterielFields();
            container.innerHTML = buildMaterielHtml(fields, data);
        }

        // Vérifie si les données sont valides
        function hasValidData(data) {
            return data && Object.keys(data).length > 0;
        }

        // Affiche un message d'absence de données
        function showNoDataMessage(container) {
            container.innerHTML = `<div class="text-danger fw-bold">Aucune information disponible pour ce matériel.</div>`;
        }

        // Retourne la liste des champs à afficher
        function getMaterielFields() {
            return [
                { label: "Constructeur", key: "constructeur" },
                { label: "Désignation", key: "designation" },
                { label: "KM", key: "km" },
                { label: "N° Parc", key: "num_parc" },
                { label: "Modèle", key: "modele" },
                { label: "Casier", key: "casier_emetteur" },
                { label: "Heures", key: "heure" },
                { label: "N° Serie", key: "num_serie" },
                { label: "Id Materiel", key: "num_matricule" },
            ];
        }

        // Construit le HTML complet à injecter dans le container
        function buildMaterielHtml(fields, data) {
            const createFieldHtml = (label, value) => `
    <li class="fw-bold">
      ${label} :
      <div class="border border-secondary border-3 rounded px-4 bg-secondary-subtle">
        ${value || "<span class='text-danger'>Non disponible</span>"}
      </div>
    </li>
  `;

            const leftColumn = fields
                .slice(0, 4)
                .map((field) => createFieldHtml(field.label, data[field.key]))
                .join("");

            const rightColumn = fields
                .slice(4)
                .map((field) => createFieldHtml(field.label, data[field.key]))
                .join("");

            return `
    <ul class="list-unstyled">
      <div class="row">
        <div class="col-12 col-md-6">
          ${leftColumn}
        </div>
        <div class="col-12 col-md-6">
          ${rightColumn}
        </div>
      </div>
    </ul>
  `;
        }
    }
}

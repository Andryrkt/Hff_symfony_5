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

        async function fetchMateriels() {
            return await fetchManager.get(`api/fetch-materiel`);
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

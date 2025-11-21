// assets/js/services/AgenceServiceManager.js

// Variable pour stocker les données pré-chargées
let agencesData = [];

// --- DOM Element Configuration ---

function capitalize(s) {
  if (typeof s !== "string" || s.length === 0) {
    return "";
  }
  return s.charAt(0).toUpperCase() + s.slice(1);
}

function createConfig(key) {
  const agenceInput = document.querySelector(`.agence${capitalize(key)}`);
  const serviceInput = document.querySelector(`.service${capitalize(key)}`);
  return { agenceInput, serviceInput };
}

const configAgenceService = {};

// --- Core Logic ---

function handleAgenceChange(configKey) {
  const config = configAgenceService[configKey];
  if (!config || !config.agenceInput) return;

  const { agenceInput, serviceInput } = config;
  const agenceId = agenceInput.value;

  if (deleteContentService(agenceId, serviceInput)) {
    return;
  }

  // Trouve l'agence et ses services dans les données pré-chargées
  const selectedAgence = agencesData.find((agence) => agence.id == agenceId);
  const services = selectedAgence ? selectedAgence.services : [];

  // Met à jour le DOM instantanément
  updateServiceOptions(services, serviceInput);
}

// --- DOM & Select Utilities ---

function updateServiceOptions(services, selectElement) {
  supprimLesOptions(selectElement);
  optionParDefaut(selectElement, "-- Choisir un Service --");

  if (Array.isArray(services)) {
    services.forEach((service) => {
      const option = document.createElement("option");
      option.value = service.id;
      option.text = service.code + " " + service.nom;
      selectElement.add(option);
    });
  }
}

function deleteContentService(agenceValue, serviceInput) {
  if (agenceValue === "") {
    supprimLesOptions(serviceInput);
    optionParDefaut(serviceInput, "-- Choisir un Service --");
    return true;
  }
  return false;
}

function supprimLesOptions(selectElement) {
  if (selectElement) {
    while (selectElement.options.length > 0) {
      selectElement.remove(0);
    }
  }
}

function optionParDefaut(selectElement, placeholder = "") {
  if (selectElement instanceof HTMLSelectElement) {
    if (
      selectElement.options.length === 0 ||
      selectElement.options[0].value !== ""
    ) {
      const defaultOption = document.createElement("option");
      defaultOption.value = "";
      defaultOption.text = placeholder || " -- Choisir une option -- ";
      defaultOption.disabled = true;
      defaultOption.selected = true;
      selectElement.add(defaultOption, 0);
    }
  }
}

// --- Initialization ---

export function initAgenceServiceHandlers() {
  const dataContainer = document.getElementById("agence-service-data");
  if (!dataContainer) {
    console.error(
      "AgenceServiceManager: Data container #agence-service-data not found."
    );
    return;
  }

  try {
    // Lit et parse les données pré-chargées depuis l'attribut data-agences
    agencesData = JSON.parse(dataContainer.dataset.agences);
  } catch (e) {
    console.error("AgenceServiceManager: Failed to parse agences data.", e);
    return;
  }

  const possibleKeys = ["emetteur", "debiteur"];
  possibleKeys.forEach((key) => {
    const agenceInput = document.querySelector(`.agence${capitalize(key)}`);
    if (agenceInput) {
      configAgenceService[key] = createConfig(key);
    }
  });

  for (const key in configAgenceService) {
    const config = configAgenceService[key];
    if (config && config.agenceInput) {
      optionParDefaut(config.serviceInput, "-- Choisir un Service --");
      config.agenceInput.addEventListener("change", () =>
        handleAgenceChange(key)
      );
    }
  }
}

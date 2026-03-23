// assets/js/utils/AgenceServiceManager.ts

// --- Types ---
interface Service {
    id: number | string;
    code: string;
    nom: string;
}

interface Agence {
    id: number | string;
    code: string;
    nom: string;
    services: Service[];
}

interface AgenceServiceConfig {
    agenceInput: HTMLSelectElement | null;
    serviceInput: HTMLSelectElement | null;
}

// Variable pour stocker les données pré-chargées
let agencesData: Agence[] = [];

// Configuration des éléments DOM
const configAgenceService: Record<string, AgenceServiceConfig> = {};

// --- Utilitaires ---

function capitalize(s: string): string {
    if (typeof s !== "string" || s.length === 0) {
        return "";
    }
    return s.charAt(0).toUpperCase() + s.slice(1);
}

function createConfig(key: string): AgenceServiceConfig {
    const agenceInput = document.querySelector(`.agence${capitalize(key)}`) as HTMLSelectElement | null;
    const serviceInput = document.querySelector(`.service${capitalize(key)}`) as HTMLSelectElement | null;
    return { agenceInput, serviceInput };
}

// --- Core Logic ---

function handleAgenceChange(configKey: string): void {
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

function updateServiceOptions(services: Service[], selectElement: HTMLSelectElement | null): void {
    supprimLesOptions(selectElement);
    optionParDefaut(selectElement, "-- Choisir un Service --");

    if (Array.isArray(services) && selectElement) {
        services.forEach((service) => {
            const option = document.createElement("option");
            option.value = String(service.id);
            option.text = service.code + " " + service.nom;
            selectElement.add(option);
        });
    }
}

function deleteContentService(agenceValue: string, serviceInput: HTMLSelectElement | null): boolean {
    if (agenceValue === "") {
        supprimLesOptions(serviceInput);
        optionParDefaut(serviceInput, "-- Choisir un Service --");
        return true;
    }
    return false;
}

function supprimLesOptions(selectElement: HTMLSelectElement | null): void {
    if (selectElement) {
        while (selectElement.options.length > 0) {
            selectElement.remove(0);
        }
    }
}

function optionParDefaut(selectElement: HTMLSelectElement | null, placeholder: string = ""): void {
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

function populateAgenceOptions(selectElement: HTMLSelectElement): void {
    const currentValue = selectElement.value;
    // Capture the text of the currently selected option in case we need to restore it
    let currentText = "";
    if (selectElement.selectedIndex >= 0) {
        currentText = selectElement.options[selectElement.selectedIndex].text;
    }

    // Attempt to preserve placeholder text from the server-rendered HTML
    let placeholderText = "-- Choisir une Agence --";
    if (selectElement.options.length > 0 && selectElement.options[0].value === "") {
        placeholderText = selectElement.options[0].text;
    }

    supprimLesOptions(selectElement);

    optionParDefaut(selectElement, placeholderText);

    let valueFound = false;

    if (Array.isArray(agencesData)) {
        agencesData.forEach((agence) => {
            const option = document.createElement("option");
            option.value = String(agence.id);
            option.text = agence.code + " " + agence.nom;
            selectElement.add(option);

            if (String(agence.id) === currentValue) {
                valueFound = true;
            }
        });
    }

    // Restore selection
    if (currentValue) {
        // If the value was not found in the new data, re-create the option
        if (!valueFound && currentText) {
            const option = document.createElement("option");
            option.value = currentValue;
            option.text = currentText;
            selectElement.add(option);
        }
        selectElement.value = currentValue;
    }
}

// --- Initialization ---

export function initAgenceServiceHandlers(): void {
    const dataContainer = document.getElementById("agence-service-data");
    if (!dataContainer) {
        return;
    }

    try {
        // Lit et parse les données pré-chargées depuis l'attribut data-agences
        const rawData = (dataContainer as HTMLElement).dataset.agences;
        agencesData = rawData ? JSON.parse(rawData) : [];
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

    // Populate Agence Options initially
    for (const key in configAgenceService) {
        const config = configAgenceService[key];
        if (config && config.agenceInput) {
            populateAgenceOptions(config.agenceInput);

            // Sauvegarder la valeur pré-sélectionnée du service avant de réinitialiser
            const preselectedServiceValue = config.serviceInput?.value || '';
            let preselectedServiceText = '';
            if (config.serviceInput && config.serviceInput.selectedIndex >= 0) {
                preselectedServiceText = config.serviceInput.options[config.serviceInput.selectedIndex].text;
            }

            optionParDefaut(config.serviceInput, "-- Choisir un Service --");
            config.agenceInput.addEventListener("change", () =>
                handleAgenceChange(key)
            );

            // Si une agence est pré-sélectionnée, charger ses services et restaurer la sélection du service
            if (config.agenceInput.value) {
                handleAgenceChange(key);

                // Restaurer la sélection du service après un court délai pour s'assurer que les options sont chargées
                if (preselectedServiceValue && config.serviceInput) {
                    setTimeout(() => {
                        if (config.serviceInput) {
                            config.serviceInput.value = preselectedServiceValue;

                            // Si la valeur n'a pas pu être sélectionnée (car absente de la liste), on la recrée
                            if (config.serviceInput.value !== preselectedServiceValue && preselectedServiceText) {
                                const option = document.createElement("option");
                                option.value = preselectedServiceValue;
                                option.text = preselectedServiceText;
                                config.serviceInput.add(option);
                                config.serviceInput.value = preselectedServiceValue;
                            }
                        }
                    }, 10);
                }
            }
        }
    }
}

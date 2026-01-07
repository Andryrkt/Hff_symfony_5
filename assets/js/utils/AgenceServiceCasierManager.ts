// assets/js/utils/AgenceServiceCasierManager.ts

// --- Types ---

interface Casier {
    id: number | string;
    nom: string;
}

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
    casiers?: Casier[];
}

interface AgenceServiceCasierConfig {
    agenceInput: HTMLSelectElement | null;
    serviceInput: HTMLSelectElement | null;
    casierInput: HTMLSelectElement | null;
    agencesData: Agence[];
}

// Configuration des éléments DOM
const configAgenceServiceCasier: Record<string, AgenceServiceCasierConfig> = {};

// --- Utilitaires ---

function capitalize(s: string): string {
    if (typeof s !== "string" || s.length === 0) {
        return "";
    }
    return s.charAt(0).toUpperCase() + s.slice(1);
}

function createConfig(key: string): AgenceServiceCasierConfig {
    const agenceInput = document.querySelector(`.agence${capitalize(key)}`) as HTMLSelectElement | null;
    const serviceInput = document.querySelector(`.service${capitalize(key)}`) as HTMLSelectElement | null;
    const casierInput = document.querySelector(`.casier${capitalize(key)}`) as HTMLSelectElement | null;
    return { agenceInput, serviceInput, casierInput, agencesData: [] };
}

// --- Core Logic ---

/**
 * Gère le changement d'agence
 * Met à jour les services ET les casiers selon l'agence sélectionnée
 */
function handleAgenceChange(configKey: string): void {
    const config = configAgenceServiceCasier[configKey];
    if (!config || !config.agenceInput) return;

    const { agenceInput, serviceInput, casierInput, agencesData } = config;
    const agenceId = agenceInput.value;

    // Réinitialiser les selects dépendants
    if (deleteContentService(agenceId, serviceInput)) {
        if (casierInput) {
            supprimLesOptions(casierInput);
            optionParDefaut(casierInput, "-- Choisir un Casier --");
            casierInput.disabled = true;
        }
        return;
    }

    const selectedAgence = agencesData.find((agence) => agence.id == agenceId);
    
    // Charger les services depuis les données pré-chargées
    const services = selectedAgence ? selectedAgence.services : [];
    updateServiceOptions(services, serviceInput);

    // Charger les casiers depuis les données pré-chargées
    if (casierInput) {
        const casiers = selectedAgence ? selectedAgence.casiers : [];
        updateCasierOptions(casiers, casierInput);
    }
}

// --- DOM & Select Utilities ---

function updateServiceOptions(services: Service[], selectElement: HTMLSelectElement | null): void {
    supprimLesOptions(selectElement);
    optionParDefaut(selectElement, "-- Choisir un Service --");

    if (Array.isArray(services) && selectElement) {
        selectElement.disabled = false;
        services.forEach((service) => {
            const option = document.createElement("option");
            option.value = String(service.id);
            option.text = service.code + " " + service.nom;
            selectElement.add(option);
        });
    }
}

function updateCasierOptions(casiers: Casier[] | undefined, selectElement: HTMLSelectElement | null): void {
    supprimLesOptions(selectElement);
    optionParDefaut(selectElement, "-- Choisir un Casier --");

    if (Array.isArray(casiers) && selectElement) {
        if (casiers.length > 0) {
            selectElement.disabled = false;
            casiers.forEach((casier) => {
                const option = document.createElement("option");
                option.value = String(casier.id);
                option.text = casier.nom;
                selectElement.add(option);
            });
        } else {
            optionParDefaut(selectElement, "-- Aucun casier disponible --");
            selectElement.disabled = true;
        }
    } else if (selectElement) {
        selectElement.disabled = true;
    }
}


function deleteContentService(agenceValue: string, serviceInput: HTMLSelectElement | null): boolean {
    if (agenceValue === "") {
        supprimLesOptions(serviceInput);
        optionParDefaut(serviceInput, "-- Choisir un Service --");
        if (serviceInput) {
            serviceInput.disabled = true;
        }
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

function populateAgenceOptions(selectElement: HTMLSelectElement, agencesData: Agence[]): void {
    const currentValue = selectElement.value;

    // Attempt to preserve placeholder text from the server-rendered HTML
    let placeholderText = "-- Choisir une Agence --";
    if (selectElement.options.length > 0 && selectElement.options[0].value === "") {
        placeholderText = selectElement.options[0].text;
    }

    supprimLesOptions(selectElement);

    optionParDefaut(selectElement, placeholderText);

    if (Array.isArray(agencesData)) {
        agencesData.forEach((agence) => {
            const option = document.createElement("option");
            option.value = String(agence.id);
            option.text = agence.code + " " + agence.nom;
            selectElement.add(option);
        });
    }

    // Restore selection
    if (currentValue) {
        selectElement.value = currentValue;
    }
}

// --- Initialization ---

export function initAgenceServiceCasierHandlers(): void {
    const possibleKeys = ["emetteur", "debiteur", "destinataire"];

    possibleKeys.forEach((key) => {
        const agenceSelector = `.agence${capitalize(key)}`;
        const containerId = `agence-service-${key}`;
        
        const agenceInput = document.querySelector(agenceSelector) as HTMLSelectElement;
        const dataContainer = document.getElementById(containerId);

        if (agenceInput && dataContainer) {
            configAgenceServiceCasier[key] = createConfig(key);
            
            try {
                const rawData = (dataContainer as HTMLElement).dataset.agences;
                configAgenceServiceCasier[key].agencesData = rawData ? JSON.parse(rawData) : [];
            } catch (e) {
                console.error(`AgenceServiceCasierManager: Failed to parse agences data for ${key}.`, e);
                return;
            }

            const config = configAgenceServiceCasier[key];
            if (config && config.agenceInput) {
                populateAgenceOptions(config.agenceInput, config.agencesData);

                // Sauvegarder les valeurs pré-sélectionnées
                const preselectedServiceValue = config.serviceInput?.value || '';
                const preselectedCasierValue = config.casierInput?.value || '';

                // Initialiser les selects dépendants
                optionParDefaut(config.serviceInput, "-- Choisir un Service --");
                if (config.casierInput) {
                    optionParDefaut(config.casierInput, "-- Choisir un Casier --");
                    config.casierInput.disabled = true;
                }

                // Attacher l'événement de changement
                config.agenceInput.addEventListener("change", () =>
                    handleAgenceChange(key)
                );

                // Si une agence est pré-sélectionnée, charger ses services et casiers
                if (config.agenceInput.value) {
                    handleAgenceChange(key);

                    // Restaurer les sélections après un court délai
                    setTimeout(() => {
                        if (preselectedServiceValue && config.serviceInput) {
                            config.serviceInput.value = preselectedServiceValue;
                        }
                        if (preselectedCasierValue && config.casierInput) {
                            config.casierInput.value = preselectedCasierValue;
                        }
                    }, 100);
                }
            }
        }
    });
}

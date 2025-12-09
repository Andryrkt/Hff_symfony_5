/**
 * Gestionnaire pour les selects Agence/Service liés
 */

/**
 * Interface pour une agence
 */
export interface Agence {
    id: number;
    code: string;
    nom: string;
    services: Service[];
}

/**
 * Interface pour un service
 */
export interface Service {
    id: number;
    code: string;
    nom: string;
}

/**
 * Configuration pour un couple Agence/Service
 */
interface AgenceServiceConfig {
    agenceInput: HTMLSelectElement;
    serviceInput: HTMLSelectElement;
}

/**
 * Gestionnaire de la relation Agence/Service
 */
export class AgenceServiceManager {
    private agencesData: Agence[] = [];
    private configs: Map<string, AgenceServiceConfig> = new Map();

    constructor(private dataContainerId: string = 'agence-service-data') { }

    /**
     * Initialise le gestionnaire
     */
    public init(): void {
        const dataContainer = document.getElementById(this.dataContainerId);

        if (!dataContainer) {
            console.error(`AgenceServiceManager: Data container #${this.dataContainerId} not found.`);
            return;
        }

        try {
            const rawData = dataContainer.dataset.agences;
            if (!rawData) {
                console.error('AgenceServiceManager: No data-agences attribute found.');
                return;
            }

            this.agencesData = JSON.parse(rawData);

            if (!Array.isArray(this.agencesData)) {
                console.error('AgenceServiceManager: data-agences is not an array.');
                return;
            }
        } catch (e) {
            console.error('AgenceServiceManager: Failed to parse agences data.', e);
            return;
        }

        this.setupConfigs();
        this.attachEventListeners();
    }

    /**
     * Configure les paires Agence/Service
     */
    private setupConfigs(): void {
        const possibleKeys = ['emetteur', 'debiteur', 'destinataire'];

        possibleKeys.forEach((key) => {
            const agenceInput = document.querySelector(
                `.agence${this.capitalize(key)}`
            ) as HTMLSelectElement;
            const serviceInput = document.querySelector(
                `.service${this.capitalize(key)}`
            ) as HTMLSelectElement;

            if (agenceInput && serviceInput) {
                this.configs.set(key, { agenceInput, serviceInput });
                this.addDefaultOption(serviceInput, '-- Choisir un Service --');
            }
        });
    }

    /**
     * Attache les écouteurs d'événements
     */
    private attachEventListeners(): void {
        this.configs.forEach((config, key) => {
            config.agenceInput.addEventListener('change', () => {
                this.handleAgenceChange(key);
            });
        });
    }

    /**
     * Gère le changement d'agence
     */
    private handleAgenceChange(configKey: string): void {
        const config = this.configs.get(configKey);
        if (!config) return;

        const { agenceInput, serviceInput } = config;
        const agenceId = agenceInput.value;

        // Si aucune agence sélectionnée, vider les services
        if (!agenceId || agenceId === '') {
            this.clearOptions(serviceInput);
            this.addDefaultOption(serviceInput, '-- Choisir un Service --');
            return;
        }

        // Trouver l'agence et ses services
        const selectedAgence = this.agencesData.find(
            (agence) => agence.id.toString() === agenceId
        );

        if (!selectedAgence) {
            console.warn(`AgenceServiceManager: Agence with id ${agenceId} not found.`);
            this.clearOptions(serviceInput);
            this.addDefaultOption(serviceInput, '-- Choisir un Service --');
            return;
        }

        const services = selectedAgence.services || [];
        this.updateServiceOptions(services, serviceInput);
    }

    /**
     * Met à jour les options du select Service
     */
    private updateServiceOptions(services: Service[], selectElement: HTMLSelectElement): void {
        this.clearOptions(selectElement);
        this.addDefaultOption(selectElement, '-- Choisir un Service --');

        if (!Array.isArray(services)) {
            console.warn('AgenceServiceManager: services is not an array.');
            return;
        }

        services.forEach((service) => {
            const option = document.createElement('option');
            option.value = service.id.toString();
            option.text = `${service.code} ${service.nom}`;
            selectElement.add(option);
        });
    }

    /**
     * Supprime toutes les options d'un select
     */
    private clearOptions(selectElement: HTMLSelectElement): void {
        if (!selectElement) {
            console.warn('AgenceServiceManager: selectElement is null or undefined.');
            return;
        }

        while (selectElement.options.length > 0) {
            selectElement.remove(0);
        }
    }

    /**
     * Ajoute une option par défaut à un select
     */
    private addDefaultOption(
        selectElement: HTMLSelectElement,
        placeholder: string = '-- Choisir une option --'
    ): void {
        if (!(selectElement instanceof HTMLSelectElement)) {
            console.warn('AgenceServiceManager: selectElement is not a HTMLSelectElement.');
            return;
        }

        // Vérifier si l'option par défaut existe déjà
        if (selectElement.options.length === 0 || selectElement.options[0].value !== '') {
            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.text = placeholder;
            defaultOption.disabled = true;
            defaultOption.selected = true;
            selectElement.add(defaultOption, 0);
        }
    }

    /**
     * Met en majuscule la première lettre d'une chaîne
     */
    private capitalize(str: string): string {
        if (typeof str !== 'string' || str.length === 0) {
            return '';
        }
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    /**
     * Récupère les agences chargées
     */
    public getAgences(): Agence[] {
        return this.agencesData;
    }

    /**
     * Récupère une agence par son ID
     */
    public getAgenceById(id: number): Agence | undefined {
        return this.agencesData.find((agence) => agence.id === id);
    }

    /**
     * Récupère les services d'une agence
     */
    public getServicesByAgenceId(agenceId: number): Service[] {
        const agence = this.getAgenceById(agenceId);
        return agence?.services || [];
    }
}

/**
 * Fonction d'initialisation pour compatibilité avec l'ancien code
 */
export function initAgenceServiceHandlers(): void {
    const manager = new AgenceServiceManager();
    manager.init();
}

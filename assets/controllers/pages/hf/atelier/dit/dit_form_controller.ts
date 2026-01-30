import { Controller } from "@hotwired/stimulus";
import { FetchManager } from "../../../../../js/utils/FetchManager";
import { AutoComplete } from "../../../../../js/components/AutoComplete";
import Swal from "sweetalert2";

export default class extends Controller {
    static targets = [
        "detailDemande", "charCount", "interneExterne", "demandeDevis",
        "debiteurAgence", "debiteurService", "numeroClient", "nomClient",
        "clientSousContrat", "numeroTel", "mailClient", "idMateriel",
        "numParc", "numSerie", "containerInfoMateriel", "reparationRealise",
        "atePolTanaContainer", "atePolTanaInput"
    ];

    declare readonly detailDemandeTarget: HTMLTextAreaElement;
    declare readonly hasDetailDemandeTarget: boolean;
    declare readonly charCountTarget: HTMLElement;
    declare readonly hasCharCountTarget: boolean;
    declare readonly interneExterneTarget: HTMLSelectElement;
    declare readonly hasInterneExterneTarget: boolean;
    declare readonly demandeDevisTarget: HTMLSelectElement;
    declare readonly hasDemandeDevisTarget: boolean;
    declare readonly debiteurAgenceTarget: HTMLSelectElement;
    declare readonly hasDebiteurAgenceTarget: boolean;
    declare readonly debiteurServiceTarget: HTMLSelectElement;
    declare readonly hasDebiteurServiceTarget: boolean;
    declare readonly numeroClientTarget: HTMLInputElement;
    declare readonly hasNumeroClientTarget: boolean;
    declare readonly nomClientTarget: HTMLInputElement;
    declare readonly hasNomClientTarget: boolean;
    declare readonly clientSousContratTarget: HTMLSelectElement;
    declare readonly hasClientSousContratTarget: boolean;
    declare readonly numeroTelTarget: HTMLInputElement;
    declare readonly hasNumeroTelTarget: boolean;
    declare readonly mailClientTarget: HTMLInputElement;
    declare readonly hasMailClientTarget: boolean;
    declare readonly idMaterielTarget: HTMLInputElement;
    declare readonly hasIdMaterielTarget: boolean;
    declare readonly numParcTarget: HTMLInputElement;
    declare readonly hasNumParcTarget: boolean;
    declare readonly numSerieTarget: HTMLInputElement;
    declare readonly hasNumSerieTarget: boolean;
    declare readonly containerInfoMaterielTarget: HTMLElement;
    declare readonly hasContainerInfoMaterielTarget: boolean;
    declare readonly reparationRealiseTarget: HTMLSelectElement;
    declare readonly hasReparationRealiseTarget: boolean;
    declare readonly atePolTanaContainerTarget: HTMLElement;
    declare readonly hasAtePolTanaContainerTarget: boolean;
    declare readonly atePolTanaInputTarget: HTMLInputElement;
    declare readonly hasAtePolTanaInputTarget: boolean;

    private fetchManager: FetchManager;
    private materielsCache: any[] | null = null;
    private materielsPromise: Promise<any[]> | null = null;
    private clientsCache: any[] | null = null;
    private clientsPromise: Promise<any[]> | null = null;

    private readonly CACHE_EXPIRY = 3600 * 1000; // 1 heure
    private readonly MATERIELS_CACHE_KEY = "hff_materiels_cache";
    private readonly CLIENTS_CACHE_KEY = "hff_clients_cache";
    private readonly MAX_CHARACTERS = 1800;

    connect() {
        this.fetchManager = new FetchManager();
        this.initializeInteractions();
        this.initializeAutocompletes();
    }

    // --- Initialisations ---

    private initializeInteractions() {
        // Initialisation du compteur de caractères
        this.updateCharCount();

        // Initialisation de la logique Interne/Externe
        this.toggleInterneExterne();

        // Initialisation de la visibilité ATE POL TANA
        this.updateAtePolTanaVisibility();
    }

    private initializeAutocompletes() {
        this.setupMaterielAutocomplete(this.idMaterielTarget, "#suggestion-idMateriel", "#loader-idMateriel");
        this.setupMaterielAutocomplete(this.numSerieTarget, "#suggestion-numSerie", "#loader-numSerie");
        this.setupMaterielAutocomplete(this.numParcTarget, "#suggestion-numParc", "#loader-numParc");

        this.setupClientAutocomplete(this.numeroClientTarget, "#suggestion-numClient", "#loader-numClient");
        this.setupClientAutocomplete(this.nomClientTarget, "#suggestion-nomClient", "#loader-nomClient");
    }

    // --- Logique Compteur de Caractères ---

    updateCharCount() {
        if (!this.hasDetailDemandeTarget || !this.hasCharCountTarget) return;

        let text = this.detailDemandeTarget.value;
        let lineBreaks = (text.match(/\n/g) || []).length;
        let adjustedLength = text.length + lineBreaks * 130;

        if (adjustedLength > this.MAX_CHARACTERS) {
            let excessCharacters = adjustedLength - this.MAX_CHARACTERS;
            while (excessCharacters > 0 && text.length > 0) {
                let lastChar = text[text.length - 1];
                excessCharacters -= (lastChar === "\n" ? 130 : 1);
                text = text.substring(0, text.length - 1);
            }
            this.detailDemandeTarget.value = text;
            adjustedLength = this.MAX_CHARACTERS;
        }

        let remaining = this.MAX_CHARACTERS - adjustedLength;
        this.charCountTarget.textContent = `Il vous reste ${Math.max(0, remaining)} caractères.`;
        this.charCountTarget.style.color = remaining <= 0 ? "red" : "gray";
    }

    // --- Logique Interne / Externe ---

    toggleInterneExterne() {
        if (!this.hasInterneExterneTarget) return;

        const isInterne = this.interneExterneTarget.value === "INTERNE";
        const isExterne = this.interneExterneTarget.value === "EXTERNE";
        const infoData = this.interneExterneTarget.dataset.informations;
        const parsedData = infoData ? JSON.parse(infoData) : { agenceId: "", serviceId: "" };

        // Champs Client
        const clientFields = [this.nomClientTarget, this.numeroClientTarget, this.numeroTelTarget, this.mailClientTarget];
        clientFields.forEach(input => {
            if (input) {
                input.disabled = isInterne;
                if (isExterne) input.setAttribute("required", "true");
                else input.removeAttribute("required");
            }
        });

        if (this.hasClientSousContratTarget) this.clientSousContratTarget.disabled = isInterne;

        // Demande de devis
        if (this.hasDemandeDevisTarget) {
            this.demandeDevisTarget.disabled = isInterne;
            this.demandeDevisTarget.value = isExterne ? "OUI" : "NON";
        }

        // Agence et Service Débiteur
        if (this.hasDebiteurAgenceTarget && this.hasDebiteurServiceTarget) {
            this.debiteurAgenceTarget.disabled = isExterne;
            this.debiteurServiceTarget.disabled = isExterne;

            if (isInterne) {
                this.debiteurAgenceTarget.value = parsedData.agenceId;
                this.debiteurServiceTarget.value = parsedData.serviceId;
            } else if (isExterne) {
                this.debiteurAgenceTarget.value = "";
                this.debiteurServiceTarget.value = "";
            }
            this.debiteurAgenceTarget.dispatchEvent(new Event('change'));
        }
    }

    // --- Logique Réparation et ATE POL TANA ---

    handleReparationChange() {
        this.updateAtePolTanaVisibility();

        if (this.reparationRealiseTarget.value === "ATE POL TANA") {
            Swal.fire({
                title: "Attention !",
                html: `Le type de document doit être "<b>Maintenance curative</b>" et la catégorie de demande est "<b>REPARATION</b>"`,
                icon: "warning",
                confirmButtonColor: "#fbbb01",
                confirmButtonText: "OUI",
            });
        }
    }

    private updateAtePolTanaVisibility() {
        const valuesAutorisees = ["ATE TANA", "ATE MAS", "ATE STAR"];
        const visible = valuesAutorisees.includes(this.reparationRealiseTarget.value);

        if (this.hasAtePolTanaContainerTarget) {
            this.atePolTanaContainerTarget.style.display = visible ? "block" : "none";
            if (!visible && this.hasAtePolTanaInputTarget) {
                this.atePolTanaInputTarget.checked = false;
            }
        }
    }

    handleAtePolTanaChange() {
        if (this.hasAtePolTanaInputTarget && this.atePolTanaInputTarget.checked) {
            Swal.fire({
                title: "Êtes-vous sûr ?",
                html: `Les travaux seront réalisés par l'${this.reparationRealiseTarget.value} en sollicitant également l'ATE POL TANA, une deuxième DIT sera créée automatiquement.<br>
                <b>Cliquer sur OUI pour confirmer et NON pour abandonner.</b>`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#fbbb01",
                cancelButtonColor: "#d33",
                confirmButtonText: "OUI",
                cancelButtonText: "NON",
                allowOutsideClick: false,
                allowEscapeKey: false,
            }).then((result) => {
                this.atePolTanaInputTarget.checked = result.isConfirmed;
            });
        }
    }

    // --- Data Fetching & Autocomplete ---

    private async fetchMateriels() {
        if (this.materielsCache) return this.materielsCache;

        const cached = localStorage.getItem(this.MATERIELS_CACHE_KEY);
        if (cached) {
            const { data, timestamp } = JSON.parse(cached);
            if (Date.now() - timestamp < this.CACHE_EXPIRY) {
                this.materielsCache = data;
                return data;
            }
        }

        if (!this.materielsPromise) {
            this.materielsPromise = this.fetchManager.get('ajax/fetch-materiel').then(data => {
                this.materielsCache = data;
                localStorage.setItem(this.MATERIELS_CACHE_KEY, JSON.stringify({ data, timestamp: Date.now() }));
                return data;
            }).finally(() => this.materielsPromise = null);
        }
        return this.materielsPromise;
    }

    private async fetchClients() {
        if (this.clientsCache) return this.clientsCache;

        const cached = localStorage.getItem(this.CLIENTS_CACHE_KEY);
        if (cached) {
            const { data, timestamp } = JSON.parse(cached);
            if (Date.now() - timestamp < this.CACHE_EXPIRY) {
                this.clientsCache = data;
                return data;
            }
        }

        if (!this.clientsPromise) {
            const url = this.numeroClientTarget.getAttribute("data-autocomplete-url") || "ajax/autocomplete/all-client";
            this.clientsPromise = this.fetchManager.get(url).then(data => {
                this.clientsCache = data;
                localStorage.setItem(this.CLIENTS_CACHE_KEY, JSON.stringify({ data, timestamp: Date.now() }));
                return data;
            }).finally(() => this.clientsPromise = null);
        }
        return this.clientsPromise;
    }

    private setupMaterielAutocomplete(input: HTMLInputElement, suggestionId: string, loaderId: string) {
        new AutoComplete({
            inputElement: input,
            suggestionContainer: document.querySelector(suggestionId) as HTMLElement,
            loaderElement: document.querySelector(loaderId) as HTMLElement,
            debounceDelay: 300,
            fetchDataCallback: this.fetchMateriels.bind(this),
            displayItemCallback: (item) => `Id: ${item.num_matricule} - Parc: ${item.num_parc} - S/N: ${item.num_serie}`,
            onSelectCallback: (item) => this.onSelectMateriel(item),
            itemToStringCallback: (item) => `${item.num_matricule} - ${item.num_parc} - ${item.num_serie}`,
            onBlurCallback: (found) => { if (!found) this.handleMaterielNotFound(input.value) }
        });
    }

    private setupClientAutocomplete(input: HTMLInputElement, suggestionId: string, loaderId: string) {
        new AutoComplete({
            inputElement: input,
            suggestionContainer: document.querySelector(suggestionId) as HTMLElement,
            loaderElement: document.querySelector(loaderId) as HTMLElement,
            debounceDelay: 300,
            fetchDataCallback: this.fetchClients.bind(this),
            displayItemCallback: (item) => `${item.num_client} - ${item.nom_client}`,
            onSelectCallback: (item) => this.onSelectClient(item),
            itemToStringCallback: (item) => `${item.num_client} - ${item.nom_client}`,
        });
    }

    private onSelectMateriel(item: any) {
        this.idMaterielTarget.value = item.num_matricule;
        this.numParcTarget.value = item.num_parc;
        this.numSerieTarget.value = item.num_serie;
        this.createMaterielInfoDisplay(item);
    }

    private onSelectClient(item: any) {
        this.numeroClientTarget.value = item.num_client;
        this.nomClientTarget.value = item.nom_client;
    }

    private handleMaterielNotFound(value: string) {
        if (!value) return;
        this.containerInfoMaterielTarget.innerHTML = `
            <div class="text-danger fw-bold">Aucun matériel trouvé pour "${value}". Veuillez choisir un élément dans la liste.</div>
        `;
    }

    // --- Affichage Informations Matériel ---

    private createMaterielInfoDisplay(data: any) {
        if (!data || Object.keys(data).length === 0) {
            this.containerInfoMaterielTarget.innerHTML = `<div class="text-danger fw-bold">Aucune information disponible.</div>`;
            return;
        }

        const fields = [
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

        const createFieldHtml = (f: any) => `
            <li class="fw-bold">
                ${f.label} :
                <div class="border border-secondary border-3 rounded px-4 bg-secondary-subtle">
                    ${data[f.key] || "<span class='text-danger'>Non disponible</span>"}
                </div>
            </li>
        `;

        const leftCol = fields.slice(0, 4).map(createFieldHtml).join("");
        const rightCol = fields.slice(4).map(createFieldHtml).join("");

        this.containerInfoMaterielTarget.innerHTML = `
            <ul class="list-unstyled">
                <div class="row">
                    <div class="col-12 col-md-6">${leftCol}</div>
                    <div class="col-12 col-md-6">${rightCol}</div>
                </div>
            </ul>
        `;
    }
}

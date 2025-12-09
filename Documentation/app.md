Lorsqu'un utilisateur seconnecte, on compare le nom d'utilisateur et le mode passe qu'il entre avec celle qui est stocké dans le Active directory
- si Ok, on le dirige vers la page d'accueil (si a le ROLE_USER)
- sinon on le propose d'entrer le vrais nom d'utilisateur et mot de passe ou consulter l'admin

Lorsque l'utilisateur est sur la page d'acceuil, des vignettes apparaissent (ils sont au nombre de 12), ses vignettes n'apparaissent pas tout sauf pour les administrateurs, les vignettes sont: Documentation, Reporting, compta, RH, Matériel, Atelier, Magasin, Appro, IT, POL, Energie, HSE

Dans chaque vignette on a:
1. Vignette Documentation => tout le monde peut accéder
- Annuaire => tout le monde peut accéder
- Plan analytique HFF => tout le monde peut accéder
- Documentation interne => tout le monde peut accéder
- Contrat => seul les utilisateur qu'on donne accès pour le contrat
  - Nouveau contrat => seul les utilisateur qui a le droit de crée
  - Consultation 
  => seul les utilisateur qui a accès pour le contrat et qui a le droit de consulté


2. Vignette Reporting => seul les utilisateur qu'on donne accès pour cette vignette
- Reporting Power BI => pour l'instant seul les utilisateur qui a accès au vignette parent
- Reporting Excel => pour l'instant seul les utilisateur qui a accès au vignette parent

3. Vignette Compta => seul les utilisateur qu'on donne accès pour cette vignette
- cours de change => pour l'instant seul les utilisateur qui a accès au vignette parent
- Demande de paiement => pour l'instant seul les utilisateur qui a accès au vignette parent
  - Nouvelle demande => pour l'instant seul les utilisateur qui a accès au vignette parent
  - Consultation => pour l'instant seul les utilisateur qui a accès au vignette parent
- Bon de Caisse => pour l'instant seul les utilisateur qui a accès au vignette parent
  - Nouvelle demande => pour l'instant seul les utilisateur qui a accès au vignette parent
  - Consultation => pour l'instant seul les utilisateur qui a accès au vignette parent

4. Vignette RH => seul les utilisateur qu'on donne accès pour cette vignette
- Ordre de mission => seul les utilisateur qu'on donne accès
  - Nouvelle demande => seul les utilisateur qui a le droit de crée
  - consultation => seul les utilisateur qui a le droit de consulté
- Mutation
  - Nouvelle demande => seul les utilisateur qui a le droit de crée
  - consultation => seul les utilisateur qui a le droit de consulté
- Congé
  - Nouvelle demande => seul les utilisateur qui a le droit de crée
  - Annulation Congé
  - consultation => seul les utilisateur qui a le droit de consulté
- Temporaires
  - Nouvelle demande => seul les utilisateur qui a le droit de crée
  - consultation => seul les utilisateur qui a le droit de consulté

5. Vignette Matériel => seul les utilisateur qu'on donne accès pour cette vignette
- Mouvement matériel => seul les utilisateur qu'on donne accès pour cette application
  - Nouvelle demande => seul les utilisateur qui a le droit de crée
  - Consultation => seul les utilisateur qui a le droit de consulté
- Casier
  - Nouvelle demande => seul les utilisateur qui a le droit de crée
  - Consultation => seul les utilisateur qui a le droit de consulté
- commandes matériels
- suivi administratif des matériels

6. Vignette Atelier => seul les utilisateur qu'on donne accès pour cette vignette
- Demande d'intervention
  - Nouvelle demande => seul les utilisateur qui a le droit de crée
  - Consultation => seul les utilisateur qui a le droit de consulté
  - Dossier DIT => seul les utilisateur qui a le droit de consulté
  - Matrice des responsabilités => seul les utilisateur qui a le droit de consulté
- Glossaire OR => seul les utilisateur qui a le droit de consulté
- Planning  => seul les utilisateur qui a le droit de consulté
- Planning détaillé => seul les utilisateur qui a le droit de consulté
- Planning interne Atelier => seul les utilisateur qui a le droit de consulté
- satisfaction client (Atelier excellence survey) => seul les utilisateur qui a le droit de consulté


7. Vignette Magasin => seul les utilisateur qu'on donne accès pour cette vignette
- OR
  - Liste à traiter => seul les utilisateur qui a le droit de consulté
  - Liste à livrer => seul les utilisateur qui a le droit de consulté
- CIS
  - Liste à traiter => seul les utilisateur qui a le droit de consulté
  - Liste à livrer => seul les utilisateur qui a le droit de consulté
- INVENTAIRE
  - Liste inventaire => seul les utilisateur qui a le droit de consulté
  - inventaire détaillé => seul les utilisateur qui a le droit de consulté
- Sortie de pieces
  - Nouvelle demande => seul les utilisateur qui a le droit de crée
- Dematérialisation
  - Devis
  - Commandes clients
  - Planning magasin
- Soumission commandes fournisseur
- Liste des cmds non placées

8. Vignette Appro => seul les utilisateur qu'on donne accès pour cette vignette
- Nouvelle DA => seul les utilisateur qui a le droit de crée
- Consultation des DA
- Liste des commandes fournisseurs

9. Vignette IT => seul les utilisateur qu'on donne accès pour cette vignette
- Nouvelle Demande => seul les utilisateur qui a le droit de crée
- Consultation
- Planning

10.  Vignette POL => seul les utilisateur qu'on donne accès pour cette vignette
- Nouvelle DLUB => pour l'instant seul les utilisateur qui a accès au vignette parent
- Consultation des DLUB => pour l'instant seul les utilisateur qui a accès au vignette parent
- Liste des commandes fournisseurs => pour l'instant seul les utilisateur qui a accès au vignette parent
- Pneumatiques => pour l'instant seul les utilisateur qui a accès au vignette parent

11.  Energie => seul les utilisateur qu'on donne accès pour cette vignette
- rapport de production centrale => pour l'instant seul les utilisateur qui a accès au vignette parent

12.  HSE => seul les utilisateur qu'on donne accès pour cette vignette
- Rapport d'incident => pour l'instant seul les utilisateur qui a accès au vignette parent
- Documentation => pour l'instant seul les utilisateur qui a accès au vignette parent


Agences :
    ['code' => '01', 'nom' => 'ANTANANARIVO'],
    ['code' => '02', 'nom' => 'CESSNA IVATO'],
    ['code' => '20', 'nom' => 'FORT-DAUPHIN'],
    ['code' => '30', 'nom' => 'AMBATOVY'],
    ['code' => '40', 'nom' => 'TAMATAVE'],
    ['code' => '50', 'nom' => 'RENTAL', ],
    ['code' => '60', 'nom' => 'PNEU - OUTIL - LUB'],
    ['code' => '80', 'nom' => 'ADMINISTRATION'],
    ['code' => '90', 'nom' => 'COMM ENERGIE'],
    ['code' => '91', 'nom' => 'ENERGIE DURABLE'],
    ['code' => '92', 'nom' => 'ENERGIE JIRAMA'],
    ['code' => 'C1', 'nom' => 'TRAVEL AIRWAYS'],

Services :

    ['code' => 'NEG', 'nom' => 'MAGASIN'],
    ['code' => 'COM', 'nom' => 'COMMERCIAL'],
    ['code' => 'ATE', 'nom' => 'ATELIER'],
    ['code' => 'CSP', 'nom' => 'CUSTOMER SUPPORT'],
    ['code' => 'GAR', 'nom' => 'GARANTIE'],
    ['code' => 'FOR', 'nom' => 'FORMATION'],
    ['code' => 'ASS', 'nom' => 'ASSURANCE'],
    ['code' => 'MAN', 'nom' => 'ENERGIE MAN'],
    ['code' => 'LCD', 'nom' => 'LOCATION'],
    ['code' => 'DIR', 'nom' => 'DIRECTION GENERALE'],
    ['code' => 'FIN', 'nom' => 'FINANCE'],
    ['code' => 'PER', 'nom' => 'PERSONNEL ET SECURITE'],
    ['code' => 'INF', 'nom' => 'INFORMATIQUE'],
    ['code' => 'IMM', 'nom' => 'IMMOBILIER'],
    ['code' => 'TRA', 'nom' => 'TRANSIT'],
    ['code' => 'APP', 'nom' => 'APPRO'],
    ['code' => 'UMP', 'nom' => 'UNITE METHODE ET PROCEDURES'],
    ['code' => 'ENG', 'nom' => 'ENGINEERIE ET REALISATIONS'],
    ['code' => 'VAN', 'nom' => 'VANILLE'],
    ['code' => 'GIR', 'nom' => 'GIROFLE'],
    ['code' => 'THO', 'nom' => 'THOMSON'],
    ['code' => 'TSI', 'nom' => 'TSIAZOMPANIRY'],
    ['code' => 'LTV', 'nom' => 'LOCATION TAMATAVE'],
    ['code' => 'LFD', 'nom' => 'LOCATION FORT DAUPHINE'],
    ['code' => 'LBV', 'nom' => 'LOCATION MORAMANGA'],
    ['code' => 'MAH', 'nom' => 'MAHAJANGA'],
    ['code' => 'NOS', 'nom' => 'NOSY BE'],
    ['code' => 'TUL', 'nom' => 'TOLIARA'],
    ['code' => 'AMB', 'nom' => 'AMBOHIMANAMBOLA'],
    ['code' => 'FLE', 'nom' => 'FLEXIBLE'],
    ['code' => 'TSD', 'nom' => 'TSIROANOMANDIDY'],
    ['code' => 'VAT', 'nom' => 'VATOMANDRY'],
    ['code' => 'BLK', 'nom' => 'BELOBABA'],
    ['code' => 'MAS', 'nom' => 'MOBILE ASSETS'],
    ['code' => 'MAP', 'nom' => 'MARCHE PUBLIC'],
    ['code' => 'ADM', 'nom' => 'ADMINISTRATION'],
    ['code' => 'LEV', 'nom' => 'LEVAGE DM'],
    ['code' => 'LR6', 'nom' => 'LOCATION RN6'],
    ['code' => 'LST', 'nom' => 'LOCATION STAR'],
    ['code' => 'LCJ', 'nom' => 'LOCATION CENTRALE JIRAMA'],
    ['code' => 'SLR', 'nom' => 'SOLAIRE'],
    ['code' => 'LGR', 'nom' => 'LOCATION GROUPES'],
    ['code' => 'LSC', 'nom' => 'LOCATION SAMCRETTE'],
    ['code' => 'C1', 'nom' => 'TRAVEL AIRWAYS'],

Relation entre Agence et service
    // Antananarivo 01
    'agence_antanarivo' => ['NEG', 'COM', 'ATE', 'CSP', 'GAR', 'ASS', 'FLE', 'MAS', 'MAP'],

    // Cessna Ivato 02
    'agence_cessna_ivato' => ['NEG', 'ATE', 'LCD'],

    // Fort-Dauphin 20
    'agence_fort_dauphin' => ['NEG', 'ATE', 'MAP'],

    // Ambatovy 30
    'agence_ambatovy' => ['NEG', 'ATE', 'MAN', 'FLE'],

    // Tamatave 40
    'agence_tamatave' => ['NEG', 'ATE', 'LCD', 'FLE', 'LEV'],

    // Rental 50
    'agence_rental' => ['LCD', 'LTV', 'LFD', 'LBV', 'LR6', 'LST', 'LSC'],

    // pneu-outil-lub 60
    'agence_pneu_outil_lub' => ['NEG', 'ATE', 'MAP'],

    // Administration 80
    'agence_administration' => ['DIR', 'FIN', 'PER', 'INF', 'IMM', 'TRA', 'APP', 'UMP'],

    // com energie 90
    'agence_comm_energie' => ['COM', 'LGR'],

    // energie durable 91
    'agence_energie_durable' => ['VAT', 'BLK', 'ENG', 'SLR'],

    //energie jirama 92
    'agence_energie_jirama' => ['MAH', 'NOS', 'TUL', 'AMB', 'LCJ', 'TSI'],

    // travel airways c1
    'agence_travel_airways' => ['C1']
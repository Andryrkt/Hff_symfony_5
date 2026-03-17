## Encore à faire:
- le formulaire de recherche dans le Dit n'est pas encore en marche
- le realiser par TANA ATE  ...
- affichage ou cache du bouton de soumission de document dans la liste DIT
- création du controller pour DW

## Déjà fait
- création de l'entity (Dit.php)
- création du DTO :
    - formulaire de création de DIT (FormDto.php)
    ce Dto contient tous les propriétés utilent pour les valeur par défaut et la soumission du formulaire de création ou duplication DIT
    - formulaire de recherche sur la liste de DIT (SearchDto.php)
    ce Dto contient tous les propriétés utilent pour la recherche sur la liste de DIT
- création du factory :
    - Formulaire de création de DIT (FormFactory.php)
    Remplissage des valeurs par défaut du formulaire de création ou duplication DIT
    - Bouton de Ellipsis (ButtonsFactory.php)
    Création des boutons d'action sur l'ellipsis
- création du form
    - Formulaire de création de DIT (DitType.php)
    - Formulaire de recherche sur la liste de DIT (DitSearchType.php)
    - Formulaire de soumission de document (OR, BC, FACTURE, DEVIS) (SoumissionDocumentAValidationType.php)
- création du controller
    - Controller de création :
        - nouvelle DIT (FormController.php)
        - Duplication DIT (DuplicationController.php)
    - Controller de liste:
        - Liste des DIT (ListController.php)
        - Détail DIT (DetailController.php)
        - Export DIT (ExportController.php)
        - Cloture DIT (ClotureController.php)
- création de Mapper
    - Mapper de DIT (DitMapper.php)
- création du Modèle
    - Modèle de DIT (Dit.php)
- création du service
    - contruction du soumision formulaire DIT (CreationHandler.php)
    - gestion des documents uploder ou crée par TCPDF (DocumentManager.php)
    - gestion des noms de ficher (GenerateFileNameService.php)
    - création de page de garde (PdfService.php)
    - gestion des filtres de recherche DIT (DitSearchFilter.php)
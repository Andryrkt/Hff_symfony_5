## Encore à faire:
- soumission de l'OR s'il est rattaché à un DA
- blockage de soumission de l'OR en avant , pendant et après => **OK**
- modification du table dit pour le colone statut_or
- envoye du fichier fusionner dans DW
- creation du test 
- creation du migration de donnée
- creation du suppretion des donnée du table ors


## BLOCAGE:
### Bloquer avant la soumission
- Bloquer si le numéro Or n'existe pas => **OK**
- bloquer si le numéro OR et le numéro DIT ne correspond pas => **OK**
-  Bloquer si il existe une ou plusieurs interventions non planifiées dans l'OR (la date planning de l'OR n'existe pas) => **OK**
- Bloquer si l'agence et service debiteur de l'OR dans IPS (informix) ne correspond pas à l'agence et service debiteur du DIT dans intranet (sqlserveur)  => **OK**
-  Bloquer si la position de l'OR est parmis 'FC', 'FE', 'CP', 'ST' => **OK**
- Bloquer si le ID materiel de l'OR ne correspond pas au ID materiel de la DIT => **OK**
-  Bloquer si la référence client est vide => **OK**
-  Bloquer si un OR est déjà en cours de validation => **OK**
- Bloquer si un OR a plusieurs service débiteur => **OK**
- Bloquer si le numéro Client de l'OR n'existe pas dans IPS => **OK**
- Bloquer si le première soumission de l'OR et le date planning est inférieur à la date du jour de soumission => **OK**

### Bloquer pendant la soumission
- le nom de fichier ne correspont pas au format attendu => **OK**
- le fichier est trop lourd supérieur à 5Mo => **OK**
- le fichier n'est pas du format PDF => **OK**
- les champs obligatoire n'est pas remplis
- le numéro OR sur le nom de fichier uploadé ne correspond pas au numéro OR de la DIT

### Bloquer après la soumission


## Déjà fait
- création de l'entity (Ors.php)
- création du DTO (OrsDto.php)
- création du Factory (OrsFactory.php)
- création du Form (OrsType.php)
- création du Controller (OrsController.php)
- création du Mapper (OrsMapper.php)
- création de l'interface de soumission de l'OR
    - Affichage du numéro DIT et OR (on ne peut pas les modifiés)
        - champ texte disabled
        - remplisser automatiquement par le DTO
    - Affichage des champs de soumission de document (champ de fichier)
        - drag and drop
        - bouton d'uplode
        - simple uplode (couleur jaune orangé)
        - plusieur uplode (couleur bleu)
        - bouton de suppréssion
        - affichage du nom et taille du fichier uplodé
    - visualisation du fichier uplodé*
- création des sérvices


#	Cas testé	Attendu
1	numeroOr vide ('')	Message : numéro OR manquant
2	numeroOr null	Message : numéro OR manquant
3	IPS retourne tableau vide	Message : n'existe pas ou différent
4	Une intervention sans date_planning_existe	Message : non planifiées
5-8	Position OR parmi FC, FE, CP, ST (dataProvider ×4)	Message : parmis 'FC', 'FE', 'CP', 'ST'
9	reference_client vide	Message : référence client est vide
10	numero_client_existe = 0	Message : client rattaché
11	id_materiel IPS ≠ DIT intranet	Message : materiel de
12-17	6 statuts bloquants (dataProvider ×6)	Message : en cours de validation
18	Agence débiteur IPS ≠ DIT	Message : agence et service debiteur
19	Plusieurs interventions avec services débiteurs différents	Message : plusieurs service débiteur
20	1ère soumission + date planning < aujourd'hui	Message : date planning est inférieur
21	Toutes les conditions valides	null retourné
22	Re-soumission : blocage statut prime sur blocage date	Message statut, PAS date planning
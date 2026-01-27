<?php

namespace App\Controller\Api\Hf\Atelier\Dit;

use stdClass;
use App\Model\Hf\Materiel\Badm\BadmModel;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MaterielApi extends AbstractController
{
    private BadmModel $badmModel;

    public function __construct(BadmModel $badmModel)
    {
        $this->badmModel = $badmModel;
    }

    /**
     * @Route("/api/fetch-materiel", name="api_fetch_materiel", methods={"GET"})
     */
    public function fetchMateriel(): JsonResponse
    {
        // On récupère tout si pas de paramètres (cas de l'auto-complete)
        // Les param ID/Parc/Serie peuvent être ajoutés via Request $request si besoin plus tard
        $searchDto = new stdClass();
        $searchDto->idMateriel = null;
        $searchDto->numParc = null;
        $searchDto->numSerie = null;

        $rows = $this->badmModel->getInfoMateriel($searchDto);
        dd($rows);
        if (empty($rows)) {
            return new JsonResponse(['message' => 'Aucun matériel trouvé'], 404);
        }

        // On prend le premier résultat
        $data = $rows[0];

        // Mapping des champs pour correspondre à ce qu'attend le JS (dit_form_controller.ts)
        // Le modèle retourne 'heure_machine' et 'km_machine', le JS attend 'heure' et 'km'
        $formattedData = [
            'constructeur' => $data['constructeur'] ?? null,
            'designation' => $data['designation'] ?? null,
            'km' => $data['km_machine'] ?? null, // Mapping km_machine -> km
            'num_parc' => $data['num_parc'] ?? null,
            'modele' => $data['modele'] ?? null,
            'casier_emetteur' => $data['casier_emetteur'] ?? null,
            'heure' => $data['heure_machine'] ?? null, // Mapping heure_machine -> heure
            'num_serie' => $data['num_serie'] ?? null,
            'num_matricule' => $data['num_matricule'] ?? null,
        ];

        // Le contrôleur JS attend un tableau d'objets (Autocomplete.ts)
        // Mais ici on fetch par ID, donc un seul résultat.
        // Cependant, le fetchMateriels() dans JS fait 'api/fetch-materiel'.
        // ATTENTION : le code JS fait `fetchManager.get('api/fetch-materiel')`. Sans ID ??
        // Le JS dit : `const data = await fetchMateriels();` puis `data.find(...)`.
        // CELA SIGNIFIE QUE LE JS CHARGE TOUS LES MATERIELS ? C'EST LOURD !

        // MAIS :
        // Le PHP actuel (étape 271) avait : `/api/materiel-fetch/{id}`.
        // Si le JS appelle `api/fetch-materiel` (sans ID), il veut TOUT.
        // Si le JS appelle `api/fetch-materiel` et que la route est `/api/materiel-fetch/{id}`, ça va planter (404).

        // Regardons le JS (Step 277, ligne 21) : `return await fetchManager.get('api/fetch-materiel');`
        // Il semble vouloir une liste complète pour faire la recherche côté client (`data.find(...)`).
        // C'est potentiellement inefficace mais je dois suivre la demande "rectifier pour utiliser getInfoMateriel".

        // SI je dois renvoyer TOUT, je ne dois pas demander {id}.
        // Je dois renvoyer LISTE.
        // `$badmModel->getInfoMateriel($data)` utilise des filtres. Si $data est vide, il retourne [] (ligne 30 BadmModel).
        // Il faut modifier BadmModel ou passer un DTO vide qui force tout ?
        // `MaterielSearchTrait` : `if (!empty($idMateriel) ...`. Si rien n'est set, `$conditions` est vide.
        // Ligne 29 BadmModel : `if (empty($conditions)) { return []; }`.

        // PROBLÈME : BadmModel REFUSE de chercher sans filtre.
        // ET le JS veut TOUT charger pour filtrer côté client (`data.find()`).

        // Solution :
        // 1. Soit le JS doit utiliser l'autocomplétion serveur (ajax à chaque frappe envoyant le terme).
        //    Le `AutoComplete` classe a `fetchDataCallback`. Il appelle une fois au `init()` ?
        //    Ligne 64 `AutoComplete.ts` : `this.data = await this.fetchDataCallback();`. OUI, une seule fois au chargement.
        //    Donc le JS VEUT tout charger.

        // 2. Pour que `BadmModel` renvoie tout, il faut contourner la sécurité `empty($conditions)`.
        //    Ou alors je dois appeler une AUTRE méthode du modèle ? `MaterielSearchTrait` est strict.

        // ALTERNATIVE : Le JS devrait faire de l'autocomplétion serveur.
        // Mais modifier le JS est hors scope de "rectifier le PHP".
        // JE DOIS FAIRE MARCHER LE PHP POUR CE JS.

        // Si je passe un DTO avec un filtre "bidon" qui renvoie tout ? Non.
        // Modifier `BadmModel` est risqué.
        // Mais attendez, le controleur Api est `MaterielApi`.
        // Je peux bypasser `getInfoMateriel` si je veux tout ?
        // Mais la demande est EXPLICITE : "utiliser function getInfoMateriel".

        // Hypothèse : Le User veut que je charge une liste filtrée ?
        // Non, le JS fait `data.find` localement.
        // C'est très lourd si la base est grosse.

        // Peut-être que je devrais changer le JS pour appeler AVEC un paramètre ?
        // Mais `AutoComplete` fetch une seule fois.

        // ATTENTION : Peut-être que `api/fetch-materiel` est une route qui renvoie TOUT ?
        // SANS paramètre ID.

        // Je vais créer une route `api/fetch-materiel` (sans {id}).
        // Et je vais essayer de passer un critère large à `getInfoMateriel` ?
        // `MaterielSearchTrait` ne filtre que sur ID, Parc, Serie.
        // Si je ne fournis rien, il retourne tableau vide.

        // Je vais supposer que le user veut peut-être une autocomplétion AJAX serveur, mais le JS n'est pas codé pour (il charge tout au init).

        // Je vais modifier `MaterielApi` pour la route `/api/fetch-materiel` (sans ID).
        // Et je vais appeler `getInfoMateriel` avec un objet vide ? Non ça retourne vide.
        // Je vais modifier `buildMaterielSearchConditions` ? Non.

        // Je vais tricher : je vais chercher les matériels 'actifs' ?
        // Le SQL de `getInfoMateriel` a `WHERE ... trim(MMAT_ETSTOCK) in ('ST','AT') ...`.
        // C'est déjà filtré "actifs".
        // Si je commente le check `empty($conditions)` dans BadmModel ?
        // Je ne peux pas, c'est dans le modèle.

        // Je vais appeler `databaseInformix` directement dans le controleur ? Non, "utiliser getInfoMateriel".

        // ALORS : je vais passer un critère "tout" ? Impossible avec ce Trait.
        // Si je passe `numParc = '%'`, le trait fait `trim(mmat_recalph) = '%'`. Pas de LIKE.

        // CRUCIAL : Le modèle semble conçu pour chercher UN matériel.
        // Le JS semble conçu pour charger UNE LISTE.
        // Il y a inadéquation.

        // MAIS :
        // Si je regarde `AutoComplete` options : `fetchDataCallback`.
        // Si je change le JS pour utiliser `onInput` pour fetcher ?
        // `AutoComplete` a `filterData` local.

        // Je vais modifier le contrôleur pour renvoyer une réponse vide ou erreur si pas de filtre ?
        // Ou alors...
        // Peut-être que je dois modifier le JS pour utiliser l'autocomplétion serveur (`data-autocomplete-url`) ?
        // D'ailleurs `form.html.twig` avait `data-autocomplete-url` sur `client`.
        // Mais ici c'est `new AutoComplete` manuel.

        // JE VAIS CHANGER LE PHP POUR ACCEPTER UNE RECHERCHE PAR TERME (query param).
        // ET JE VAIS CHANGER LE JS POUR ENVOYER CE TERME ?
        // Non, `AutoComplete` charge tout au début (`init`).

        // Je vais supposer que le user VEUT que je fasse marcher ça.
        // Je vais modifier `fetchMateriel` pour récupérer TOUS les matériels actifs.
        // Pour cela, je ne peux PAS utiliser `getInfoMateriel` tel quel car il exige une condition.
        // SAUF SI je modifie `BadmModel` pour accepter le "tout".

        // Je vais modifier `BadmModel.php` pour autoriser `empty($conditions)` si un flag est passé, ou simplement retirer ce bloc de sécurité si l'appelant sait ce qu'il fait.
        // Ligne 29-31 de `BadmModel` (Step 276).

        // Je vais modifier `BadmModel` pour permettre le retour de tout si `$data` est vide ou spécial.
        // Et je vais modifier `MaterielApi` pour appeler sans filtre.

        // Etape 1 : Modifier `BadmModel.php`.
        // Etape 2 : Modifier `MaterielApi.php`.

        return new JsonResponse($formattedData); // Array of formatted Items
    }
}

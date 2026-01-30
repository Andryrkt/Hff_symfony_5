<?php

namespace App\Controller\Api\Hf\Atelier\Dit;

use App\Model\DatabaseInformix;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller pour la gestion de l'autocomplétion des clients via Informix.
 */
class ClientAutocompleteController extends AbstractController
{
    private $databaseInformix;

    public function __construct(DatabaseInformix $databaseInformix)
    {
        $this->databaseInformix = $databaseInformix;
    }

    /**
     * Récupère le nom et le numéro de tous les clients HFF depuis Informix.
     * 
     * @Route("/ajax/autocomplete/all-client", name="ajax_autocomplete_all_client", methods={"GET"})
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return JsonResponse
     */
    public function getAllClients(): JsonResponse
    {
        try {
            // Établir la connexion à Informix
            $this->databaseInformix->connect();

            // Requête complète pour le cache
            $query = "SELECT trim(cbse_nomcli) as nom_client, cbse_numcli as num_client 
                      FROM cli_bse, cli_soc 
                      WHERE cbse_numcli = csoc_numcli 
                      AND csoc_soc ='HF'";

            // Exécuter la requête
            $result = $this->databaseInformix->executeQuery($query);

            // Récupérer les résultats
            $data = $this->databaseInformix->fetchResults($result);

            // Fermer la connexion
            $this->databaseInformix->close();

            return new JsonResponse($data);
        } catch (\Exception $e) {
            // En cas d'erreur, retourner un message JSON
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}

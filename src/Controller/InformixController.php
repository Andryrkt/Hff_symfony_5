<?php

namespace App\Controller;

use App\Model\DatabaseInformix;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class InformixController extends AbstractController
{
    private $databaseInformix;

    public function __construct(DatabaseInformix $databaseInformix)
    {
        $this->databaseInformix = $databaseInformix;
    }

    /**
     * @Route("/test-informix", name="test_informix")
     *
     * @return Response
     */
    public function queryInformix(): Response
    {
        try {
            // Établir la connexion
            $this->databaseInformix->connect();

            // Exécuter une requête
            $query = "SELECT * FROM mat_mat";
            $result = $this->databaseInformix->executeQuery($query);

            // Récupérer les résultats
            $data = $this->databaseInformix->fetchResults($result);
dd($data);
            // Fermer la connexion
            $this->databaseInformix->close();

            // Retourner une réponse
            return $this->json($data);
        } catch (\Exception $e) {
            // Gérer les erreurs
            return $this->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

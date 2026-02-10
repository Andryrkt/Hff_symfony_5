<?php

namespace App\Controller\Hf\Atelier\Dit\Liste;

use App\Entity\Hf\Atelier\Dit\Dit;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Hf\Atelier\Dit\PdfService;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\Hf\Atelier\Dit\DitRepository;
use App\Constants\Hf\Atelier\Dit\StatutDitConstants;
use App\Repository\Admin\Statut\StatutDemandeRepository;
use App\Constants\Admin\Historisation\TypeDocumentConstants;
use App\Constants\Admin\Historisation\TypeOperationConstants;
use App\Service\Historique_operation\HistoriqueOperationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * @Route("/hf/atelier/dit/liste")
 */
class ClotureController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private StatutDemandeRepository $statutDemandeRepository;
    private ParameterBagInterface $params;
    private HistoriqueOperationService $historiqueOperationService;

    public function __construct(
        EntityManagerInterface $entityManager,
        StatutDemandeRepository $statutDemandeRepository,
        ParameterBagInterface $params,
        HistoriqueOperationService $historiqueOperationService
    ) {
        $this->entityManager = $entityManager;
        $this->statutDemandeRepository = $statutDemandeRepository;
        $this->params = $params;
        $this->historiqueOperationService = $historiqueOperationService;
    }
    /**
     * @Route("/cloture/{numDit}", name="hf_atelier_dit_liste_cloture")
     */
    public function index(
        string $numDit,
        DitRepository $ditRepository,
        PdfService $pdfService
    ) {
        try {
            $dit = $ditRepository->findOneBy(['numeroDit' => $numDit]); // récupération de l'information du DIT à annuler

            if (!$dit) {
                throw new \Exception("DIT numéro $numDit non trouvé");
            }

            $this->modificationTableDit($dit);

            $numero = $dit->getNumeroDit();
            $fileNameUplode = 'fichier_cloturer_annuler_' . $numero . '.csv';
            $filePathUplode = $this->params->get('docuware_directory') . '/dit/' . $numero . '/' . $fileNameUplode;
            $fileNameDw = 'fichier_cloturer_annuler' . '.csv';
            $headers = ['numéro DIT', 'statut'];
            $numDits = $ditRepository->getNumDitAAnnuler();

            $data = [];
            foreach ($numDits as $numDit) {
                $data[] = [
                    $numDit,
                    'Clôturé annulé'
                ];
            }

            if (file_exists($filePathUplode)) {
                unlink($filePathUplode);
            }

            $this->ajouterDansCsv($filePathUplode, $data, $headers);

            $filePathDw = 'ftp://ftp.docuware-online.de/VhhlMDUEYTbzBI_A8C6lpRt86g-wKO2lXFKfXfSP/data/' . $fileNameDw;
            $pdfService->copyCsvCloturerAnnulerToDw($filePathDw, $filePathUplode);

            $message = "La DIT a été clôturée avec succès.";
            $success = true;
        } catch (\Exception $e) {
            $message = "Erreur lors de la clôture de la DIT : " . $e->getMessage();
            $success = false;

            // Log l'erreur si nécessaire
            // $this->logger->error($message, ['exception' => $e]);

            // Optionnel : re-lancer l'exception si vous voulez que l'erreur remonte plus haut
            // throw $e;
        } finally {
            // Enregistrement dans l'historique dans tous les cas (succès ou échec)
            $numero = isset($numero) ? $numero : $numDit;

            $this->historiqueOperationService->enregistrer(
                $numero,
                TypeOperationConstants::TYPE_OPERATION_ANNULER_NAME,
                TypeDocumentConstants::TYPE_DOCUMENT_DIT_CODE,
                $success,
                $message
            );

            // Si une erreur s'est produite, vous pourriez vouloir renvoyer une réponse d'erreur
            if (!$success) {
                // Par exemple, si vous utilisez Symfony avec des contrôleurs HTTP :
                // return new JsonResponse(['error' => $message], 500);
                // Ou selon votre contexte d'application
            }
        }
    }

    private function modificationTableDit(Dit $dit)
    {
        $statutCloturerAnnuler = $this->statutDemandeRepository->findOneBy(['description' => StatutDitConstants::STATUT_CLOTUREE_ANNULEE]);
        $dit
            ->setStatutDemande($statutCloturerAnnuler)
            ->setEstAnnuler(true)
            ->setDateAnnulation(new \DateTime())
        ;
        $this->entityManager->persist($dit);
        $this->entityManager->flush();
    }

    private function ajouterDansCsv($filePath, $data, $headers = null)
    {
        $fichierExiste = file_exists($filePath);
        $handle = fopen($filePath, 'a');

        // Si le fichier est nouveau, ajoute un BOM UTF-8
        if (!$fichierExiste) {
            fwrite($handle, "\xEF\xBB\xBF"); // Ajout du BOM
        }

        // Fonction pour écrire une ligne sans guillemets
        $ecrireLigne = function ($ligne) use ($handle) {
            $ligneUtf8 = array_map(function ($field) {
                if (is_array($field)) {
                    // Tu peux choisir un séparateur ou une structure ici
                    $field = implode(';', $field);
                }
                return mb_convert_encoding($field, 'UTF-8');
            }, $ligne);
            fwrite($handle, implode(';', $ligneUtf8) . PHP_EOL); // tu peux changer ';' par ',' si nécessaire
        };
        // Écrit les en-têtes si le fichier est nouveau
        if (!$fichierExiste && $headers !== null) {
            $ecrireLigne($headers);
        }

        // Écrit les données sans guillemets
        foreach ($data as $ligne) {
            $ecrireLigne($ligne);
        }

        fclose($handle);
    }
}

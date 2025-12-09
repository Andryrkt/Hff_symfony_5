<?php

namespace App\Controller\Hf\Rh\Dom\Liste;

use App\Controller\Base\AbstractExcelExportController;
use App\Dto\Hf\Rh\Dom\DomSearchDto;
use App\Service\Utils\Export\ExcelService;
use App\Repository\Hf\Rh\Dom\DomRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Contrôleur d'export Excel pour les DOM (Demandes d'Ordre de Mission)
 * 
 * @Route("/rh/ordre-de-mission")
 */
final class DomExportExcelController extends AbstractExcelExportController
{
    private ?SessionInterface $session = null;
    private ?DomRepository $domRepository = null;

    /**
     * Export des données dans un fichier excel
     * @Route("/export-excel-dom", name="dom_export_excel")
     */
    public function export(ExcelService $excelService, Request $request, DomRepository $domRepository): Response
    {
        // Stocker les dépendances pour utilisation dans getRows()
        $this->session = $request->getSession();
        $this->domRepository = $domRepository;

        return $this->exportToExcel($excelService);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders(): array
    {
        return [
            "Statut",
            "SousType",
            "N°DOM",
            "Date demande",
            "Motif de déplacement",
            "Matricule",
            "Agence/Service",
            "Date de début",
            "Date de fin",
            "Client",
            "Lieu d'intervention",
            "Total général payer",
            "Devis"
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getRows(): array
    {
        $doms = $this->domRepository->findFilteredExcel($this->getDomSearchDto($this->session));
        $data = [];

        foreach ($doms as $dom) {
            $data[] = [
                $dom->getIdStatutDemande() ? $dom->getIdStatutDemande()->getDescription() : '',
                $dom->getSousTypeDocument() ? $dom->getSousTypeDocument()->getCodeSousType() : '',
                $dom->getNumeroOrdreMission(),
                $dom->getDateDemande() ? $dom->getDateDemande()->format('d/m/Y') : '',
                $dom->getMotifDeplacement(),
                $dom->getMatricule(),
                $dom->getLibelleCodeAgenceService(),
                $dom->getDateDebut(),
                $dom->getDateFin(),
                $dom->getClient(),
                $dom->getLieuIntervention(),
                str_replace('.', '', $dom->getTotalGeneralPayer()),
                $dom->getDevis()
            ];
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilename(): string
    {
        return 'dom_export_' . date('Y-m-d');
    }

    /**
     * Récupère le DTO de recherche depuis la session
     */
    private function getDomSearchDto(SessionInterface $session): ?DomSearchDto
    {
        $domSearchDto = $session->get('dom_search_dto');

        if (!$domSearchDto) {
            $domSearchDto = new DomSearchDto();
        }

        return $domSearchDto;
    }
}

<?php

namespace App\Controller\Hf\Rh\Dom\Liste;

use App\Dto\Hf\Rh\Dom\DomSearchDto;
use App\Service\Utils\Export\ExcelService;
use App\Repository\Hf\Rh\Dom\DomRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/rh/ordre-de-mission")
 */
class DomExportExcelController extends AbstractController
{
    /**
     * Export des données dans un fichier excel
     * @Route("/export-excel-dom", name="dom_export_excel")
     */
    public function export(ExcelService $excelService, Request $request, DomRepository $domRepository): Response
    {
        $data = [$this->header(), ...$this->body($request->getSession(), $domRepository)];

        return $excelService->exportToBrowser($data, 'utilisateurs_2025');
    }

    private function header(): array
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

    private function body(SessionInterface $session, DomRepository $domRepository)
    {
        $doms = $domRepository->findFilteredExcel($this->getDomSearchDto($session));
        $data = [];
        foreach ($doms as  $dom) {
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

    private function getDomSearchDto(SessionInterface $session): ?DomSearchDto
    {
        $domSearchDto = $session->get('dom_search_dto');

        if (!$domSearchDto) {
            $domSearchDto = new DomSearchDto();
        }

        return $domSearchDto;
    }
}

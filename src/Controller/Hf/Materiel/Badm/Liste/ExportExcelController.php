<?php

namespace App\Controller\Hf\Materiel\Badm\Liste;

use App\Dto\Hf\Materiel\Badm\SearchDto;
use App\Contract\Dto\SearchDtoInterface;
use App\Service\Utils\Export\ExcelService;
use App\Mapper\Hf\Materiel\Badm\BadmMapper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\Hf\Materiel\Badm\BadmRepository;
use App\Controller\Base\AbstractExcelExportController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @Route("/hf/materiel/badm")
 */
final class ExportExcelController extends AbstractExcelExportController
{
    private ?SessionInterface $session = null;
    private ?BadmRepository $badmRepository = null;
    private ?BadmMapper $badmMapper = null;

    /**
     * @Route("/export-excel", name="hf_materiel_badm_export_excel_index")
     */
    public function index(
        Request $request,
        ExcelService $excelService,
        BadmRepository $badmRepository,
        BadmMapper $badmMapper
    ) {
        // Stocker les dépendances pour utilisation dans getRows()
        $this->session = $request->getSession();
        $this->badmRepository = $badmRepository;
        $this->badmMapper = $badmMapper;

        return $this->exportToExcel($excelService);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders(): array
    {
        return [
            "Statut",
            "N°BADM",
            "Date demande",
            "Mouvement",
            "Id matériel",
            "Ag/Serv émetteur",
            "N° Parc",
            "Casier émetteur",
            "Casier destinataire"
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getRows(): array
    {
        $badms = $this->badmRepository->findFilteredExcel($this->getSearchDto($this->session));
        $secondFormDtos = array_map(function ($item) {
            return $this->badmMapper->reverseMap($item);
        }, $badms);
        $data = [];

        foreach ($secondFormDtos as $secondFormDto) {
            $data[] = [
                $secondFormDto->statutDemande ? $secondFormDto->statutDemande->getDescription() : '',
                $secondFormDto->numeroBadm,
                $secondFormDto->dateDemande ? $secondFormDto->dateDemande->format('d/m/Y') : '',
                $secondFormDto->typeMouvement ? $secondFormDto->typeMouvement->getDescription() : '',
                $secondFormDto->idMateriel,
                $secondFormDto->emetteur['agence']->getCode() . '-' . $secondFormDto->emetteur['service']->getCode(),
                $secondFormDto->numParc,
                $secondFormDto->emetteur['casier']->getNom(),
                $secondFormDto->destinataire['casier'] ? $secondFormDto->destinataire['casier']->getNom() : '',
            ];
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilename(): string
    {
        return 'badm_export_' . date('Y-m-dH:i:s');
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchDto(SessionInterface $session): SearchDtoInterface
    {
        $searchDto = $session->get('badm_search_dto');

        if (!$searchDto) {
            $searchDto = new SearchDto();
        }

        return $searchDto;
    }
}

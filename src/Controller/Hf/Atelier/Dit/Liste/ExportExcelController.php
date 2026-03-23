<?php

namespace App\Controller\Hf\Atelier\Dit\Liste;

use App\Dto\Hf\Atelier\Dit\SearchDto;
use App\Mapper\Hf\Atelier\Dit\Mapper;
use App\Contract\Dto\SearchDtoInterface;
use App\Service\Utils\Export\ExcelService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\Hf\Atelier\Dit\DitRepository;
use App\Controller\Base\AbstractExcelExportController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/hf/atelier/dit")
 */
class ExportExcelController extends AbstractExcelExportController
{
    private ?SessionInterface $session = null;
    private ?DitRepository $ditRepository = null;
    private ?Mapper $ditMapper = null;

    /**
     * @Route("/export-excel", name="hf_atelier_dit_export_excel_index")
     */
    public function index(
        Request $request,
        ExcelService $excelService,
        DitRepository $ditRepository,
        Mapper $ditMapper
    ) {
        // Stocker les dépendances pour utilisation dans getRows()
        $this->session = $request->getSession();
        $this->ditRepository = $ditRepository;
        $this->ditMapper = $ditMapper;

        return $this->exportToExcel($excelService);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders(): array
    {
        return [
            'Statut',
            'N° DIT',
            'Réalisé par',
            'Type Document',
            'Niveau',
            'Catégorie de Demande',
            'N°Serie',
            'N°Parc',
            'date demande',
            'Int/Ext',
            'Emetteur',
            'Débiteur',
            'Objet',
            'sectionAffectee',
            'N° devis',
            'Statut Devis',
            'N°Or',
            'Statut Or',
            'Statut facture',
            'RI',
            'Nbre Pj',
            'utilisateur',
            'Marque',
            'Casier'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getRows(): array
    {
        $dits = $this->ditRepository->findFilteredExcel($this->getSearchDto($this->session));
        $dtos = array_map(function ($item) {
            return $this->ditMapper->reverseMap($item);
        }, $dits);
        $data = [];

        foreach ($dtos as $dto) {
            $data[] = [
                $dto->statutDemande->getDescription(),
                $dto->numeroDit,
                $dto->reparationRealise,
                $dto->typeDocument->getDescription(),
                $dto->niveauUrgence->getCode(),
                $dto->categorieDemande->getLibelleCategorieAteApp(),
                $dto->numSerie,
                $dto->numParc,
                $dto->dateDemande,
                $dto->interneExterne,
                $dto->emetteur['agence']->getCode() . '-' . $dto->emetteur['service']->getCode(),
                $dto->debiteur['agence']->getCode() . '-' . $dto->debiteur['service']->getCode(),
                $dto->objetDemande,
                $dto->sectionAffectee,
                $dto->numeroDevisRattacher,
                $dto->statutDevis,
                $dto->numeroOr,
                $dto->statutOr,
                $dto->etatFacturation,
                $dto->ri,
                $dto->nbrPj,
                $dto->demandeur->getUserIdentifier(),
                $dto->marque,
                $dto->casier
            ];
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilename(): string
    {
        return 'dit_export_' . date('Y-m-dH:i:s');
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchDto(SessionInterface $session): SearchDtoInterface
    {
        $searchDto = $session->get('dit_search_dto');

        if (!$searchDto) {
            $searchDto = new SearchDto();
        }

        return $searchDto;
    }
}

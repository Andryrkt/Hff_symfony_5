<?php

namespace App\Factory\Hf\Atelier\Dit\Soumission\Ors;

use App\Constants\Hf\Atelier\Dit\Soumission\Ors\StatutOrConstant;
use App\Dto\Hf\Atelier\Dit\Soumission\Ors\OrsDto;
use App\Entity\Admin\PersonnelUser\User;
use App\Mapper\Hf\Atelier\Dit\Soumission\Ors\OrsComparaisonMapper;
use App\Mapper\Hf\Atelier\Dit\Soumission\Ors\OrsMapper;
use App\Mapper\Hf\Atelier\Dit\Soumission\Ors\OrsParInterventionMapper;
use App\Mapper\Hf\Atelier\Dit\Soumission\Ors\PieceFaibleAchatMapper;
use App\Mapper\Hf\Atelier\Dit\Soumission\Ors\TotalOrsParInterventionMapper;
use App\Model\Hf\Atelier\Dit\Soumission\Ors\OrsModel;
use App\Repository\Hf\Atelier\Dit\Ors\OrsRepository;
use App\Service\Utils\NumeroGeneratorService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Security;


class OrsFactory
{
    private OrsRepository $orsRepository;
    private NumeroGeneratorService $numeroGeneratorService;
    private OrsParInterventionMapper $orsParInterventionMapper;
    private TotalOrsParInterventionMapper $totalOrsParInterventionMapper;
    private PieceFaibleAchatMapper $pieceFaibleAchatMapper;
    private OrsComparaisonMapper $orsComparaisonMapper;
    private OrsMapper $orsMapper;
    private OrsModel $orsModel;
    private ParameterBagInterface $parameters;
    private Security $security;

    public function __construct(
        OrsRepository $orsRepository,
        NumeroGeneratorService $numeroGeneratorService,
        OrsParInterventionMapper $orsParInterventionMapper,
        TotalOrsParInterventionMapper $totalOrsParInterventionMapper,
        PieceFaibleAchatMapper $pieceFaibleAchatMapper,
        OrsComparaisonMapper $orsComparaisonMapper,
        OrsMapper $orsMapper,
        OrsModel $orsModel,
        ParameterBagInterface $parameters,
        Security $security
    ) {
        $this->orsRepository = $orsRepository;
        $this->numeroGeneratorService = $numeroGeneratorService;
        $this->orsParInterventionMapper = $orsParInterventionMapper;
        $this->totalOrsParInterventionMapper = $totalOrsParInterventionMapper;
        $this->pieceFaibleAchatMapper = $pieceFaibleAchatMapper;
        $this->orsComparaisonMapper = $orsComparaisonMapper;
        $this->orsMapper = $orsMapper;
        $this->orsModel = $orsModel;
        $this->parameters = $parameters;
        $this->security = $security;
    }

    public function create(string $numeroDit, string $numeroOr): OrsDto
    {
        $dto = new OrsDto();

        /** @var User */
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new \RuntimeException('User not authenticated');
        }

        $dto->numeroDit = $numeroDit;
        $dto->numeroOr = (int) $numeroOr;
        $dto->emailDemandeur = $user->getEmail();

        return $dto;
    }

    public function enrichissementDto(OrsDto $dto)
    {
        $lastVersion = $this->orsRepository->getNumeroVersion($dto->numeroOr);
        $dto->numeroVersion = $this->numeroGeneratorService->simpleIncrement($lastVersion);
        $dto->statut = StatutOrConstant::SOUMIS_A_VALIDATION;
        $dto->dateDemande = new \DateTime();

        // Récupération unique des données Informix pour éviter les requêtes multiples
        $infoSurLesOrs = $this->orsModel->getInfoOrs($dto->numeroDit, $dto->numeroOr);

        // Extraction des informations globales (numeroDevis, flags) de l'OR
        $this->extraireInfosGlobales($dto, $infoSurLesOrs);

        // Récupération automatique par ligne d'interventions de l'OR (Récapitulation de l'OR)
        $dto->orsParInterventionDtos = $this->orsParInterventionMapper->mapFromRawData($infoSurLesOrs);

        // Récupération du total des OR par intervention
        $dto->totalOrsParIntervention = [$this->totalOrsParInterventionMapper->calculateTotals($dto)];
        // Récupération automatique des pièces faible achat
        $dto->pieceFaibleAchatDtos = $this->pieceFaibleAchatMapper->mapToDtos($dto, $infoSurLesOrs);
        $dto->pieceFaibleActiviteAchat = !empty($dto->pieceFaibleAchatDtos);

        // Comparaison Avant / Après
        $orsAvant = $lastVersion ? $this->orsRepository->findByOrAndVersion($dto->numeroOr, $lastVersion) : [];
        $orsApres = $this->orsMapper->map($dto);

        // Construction de la map des dates de planning pour éviter les requêtes dans le mapper
        $datePlanningMap = [];
        foreach ($dto->orsParInterventionDtos as $itvDto) {
            $datePlanningMap[$itvDto->numeroItv] = $itvDto->datePlanning;
        }

        $dto->orsApresDtos = $this->orsComparaisonMapper->mapToComparaisonDtos($orsAvant, $orsApres, $datePlanningMap);
    }

    private function extraireInfosGlobales(OrsDto $dto, array $infoSurLesOrs): void
    {
        $dto->estPieceSortieMagasin = 'NON';
        $dto->estPieceAchatLocaux = 'NON';
        $dto->estPiecePol = 'NON';
        $dto->numeroDevis = null;

        $piecesMagasin = explode(',', str_replace("'", "", $this->parameters->get('app.constructeurs.pieces_magasin')));
        $achatLocaux = explode(',', str_replace("'", "", $this->parameters->get('app.constructeurs.achat_locaux')));
        $lubrifiants = explode(',', str_replace("'", "", $this->parameters->get('app.constructeurs.lub')));

        foreach ($infoSurLesOrs as $info) {
            // Un seul passage suffit pour le numéro de devis
            if (null === $dto->numeroDevis && !empty($info['numero_devis'])) {
                $dto->numeroDevis = (int) $info['numero_devis'];
            }

            $constr = $info['constructeur'] ?? '';
            $ref = $info['reference'] ?? '';

            // Flag Pièce Sortie Magasin
            if ($dto->estPieceSortieMagasin === 'NON' && in_array($constr, $piecesMagasin)) {
                // Application de la même logique d'exclusion que dans OrsModel::getPieceSortieMagasin
                if (!str_ends_with($ref, '-L') && !str_ends_with($ref, '-CTRL')) {
                    if ((float)($info['montant_piece'] ?? 0) > 0) {
                        $dto->estPieceSortieMagasin = 'OUI';
                    }
                }
            }

            // Flag Achat Locaux
            if ($dto->estPieceAchatLocaux === 'NON' && in_array($constr, $achatLocaux)) {
                if ((float)($info['montant_achats_locaux'] ?? 0) > 0) {
                    $dto->estPieceAchatLocaux = 'OUI';
                }
            }

            // Flag Pièce Pol (Lubrifiants)
            if ($dto->estPiecePol === 'NON' && in_array($constr, $lubrifiants)) {
                if ((float)($info['montant_lubrifiants'] ?? 0) > 0) {
                    $dto->estPiecePol = 'OUI';
                }
            }
        }
    }

    private function getNumeroDevis(OrsDto $dto)
    {
        return $this->orsModel->getNumeroDevis($dto->numeroOr);
    }

    private function getPieceSortieMagasin(OrsDto $dto)
    {
        return $this->orsModel->getPieceSortieMagasin($dto->numeroOr);
    }

    private function getPieceAchatLocaux(OrsDto $dto)
    {
        return $this->orsModel->getPieceAchatLocaux($dto->numeroOr);
    }

    private function getPiecePol(OrsDto $dto)
    {
        return $this->orsModel->getPiecePol($dto->numeroOr);
    }
}

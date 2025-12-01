<?php

namespace App\Repository\Hf\Rh\Dom;

use App\Entity\Hf\Rh\Dom\Dom;
use Doctrine\ORM\QueryBuilder;
use App\Dto\Hf\Rh\Dom\DomSearchDto;
use App\Entity\Admin\PersonnelUser\User;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Hf\Rh\Dom\SousTypeDocument;
use Symfony\Component\Security\Core\Security;
use App\Service\Security\ContextAccessService;
use App\Repository\Traits\DynamicContextFilterTrait;
use App\Constants\Admin\Historisation\TypeDocumentConstants;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Dom>
 *
 * @method Dom|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dom|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dom[]    findAll()
 * @method Dom[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DomRepository extends ServiceEntityRepository
{
    use DynamicContextFilterTrait;

    private const TYPE_MISSION_ECARTER = [
        SousTypeDocument::CODE_COMPLEMENT,
        SousTypeDocument::CODE_TROP_PERCU
    ];

    private ContextAccessService $contextAccessService;
    private Security $security;

    public function __construct(ManagerRegistry $registry, ContextAccessService $contextAccessService, Security $security)
    {
        parent::__construct($registry, Dom::class);
        $this->contextAccessService = $contextAccessService;
        $this->security = $security;
    }

    public function add(Dom $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Dom $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    /**
     * Vérifie si une mission existe déjà pour un matricule donné sur une période qui se chevauche.
     */
    public function hasOverlappingMission(
        string $matricule,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate
    ): bool {
        // Adaptez 'm.personnel' et 'p.matricule' si vos relations/propriétés sont différentes
        // Adaptez 'm.dateDebut' et 'm.dateFin' aux noms de vos champs de date dans l'entité Mission
        $qb = $this->createQueryBuilder('m')
            ->select('count(m.id)')
            ->innerJoin('m.sousTypeDocument', 't')
            ->where('m.matricule = :matricule')
            ->andWhere('t.codeSousType NOT IN (:typeMission)')
            // La nouvelle mission ne doit pas commencer pendant une mission existante
            // ET la nouvelle mission ne doit pas se terminer pendant une mission existante
            // ET la nouvelle mission ne doit pas englober une mission existante
            ->andWhere('m.dateDebut < :endDate AND m.dateFin > :startDate')
            ->setParameters([
                'matricule' => $matricule,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'typeMission' => self::TYPE_MISSION_ECARTER
            ]);

        $count = $qb->getQuery()->getSingleScalarResult();

        return $count > 0;
    }

    public function getInfoDOMMatrSelet(string $matricule)
    {
        return $this->createQueryBuilder('d')
            ->select('d.dateDebut, d.dateFin')
            ->where('d.matricule = :matricule')
            ->andWhere('d.idStatutDemande not in (:excludStatut)')
            ->setParameters([
                'matricule' => $matricule,
                'excludStatut' => []
            ])
            ->getQuery()
            ->getArrayResult()
        ;
    }

    public function findPaginatedAndFiltered(int $page = 1, int $limit = 10, DomSearchDto $domSearchDto)
    {
        // Mapping des colonnes triables (whitelist de sécurité)
        $sortableColumns = [
            'numeroOrdreMission' => 'd.numeroOrdreMission',
            'matricule' => 'd.matricule',
            'dateDemande' => 'd.dateDemande',
            'dateDebut' => 'd.dateDebut',
            'dateFin' => 'd.dateFin',
            'typeDocument' => 'td.libelleSousType',
            'statut' => 's.description',
        ];

        // Récupérer les paramètres de tri depuis le DTO
        $sortBy = $domSearchDto->sortBy ?? 'numeroOrdreMission';
        $sortOrder = strtoupper($domSearchDto->sortOrder ?? 'DESC');

        // Validation de sécurité
        if (!isset($sortableColumns[$sortBy])) {
            $sortBy = 'numeroOrdreMission'; // Valeur par défaut sécurisée
        }
        if (!in_array($sortOrder, ['ASC', 'DESC'])) {
            $sortOrder = 'DESC'; // Valeur par défaut sécurisée
        }

        // Récupérer la limite depuis le DTO
        $limit = $domSearchDto->limit ?? 50;

        // 1. Créer le QueryBuilder avec les jointures et chargement eager des relations
        $queryBuilder = $this->createQueryBuilder('d')
            ->leftJoin('d.sousTypeDocument', 'td')
            ->addSelect('td')  // Évite le problème N+1
            ->leftJoin('d.idStatutDemande', 's')
            ->addSelect('s');  // Évite le problème N+1

        // 2. Appliquer les filtres de recherche
        $this->filtred($queryBuilder, $domSearchDto);
        $this->filtredDate($queryBuilder, $domSearchDto);
        $this->filtredStatut($queryBuilder, $domSearchDto);
        $this->filtredAgenceService($queryBuilder, $domSearchDto);

        // 3. Ordre et pagination
        $queryBuilder->orderBy($sortableColumns[$sortBy], $sortOrder)
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        // 4. Exécuter avec Paginator
        $paginator = new DoctrinePaginator($queryBuilder);
        $totalItems = count($paginator);
        $lastPage = ceil($totalItems / $limit);

        return [
            'data'        => iterator_to_array($paginator->getIterator()),
            'totalItems'  => $totalItems,
            'currentPage' => $page,
            'lastPage'    => $lastPage,
        ];
    }

    /**
     * Filtre pour le statut
     * *on n'affiche pas les DOM qui a le statut annulé sauf si on le recherche
     */
    private function filtredStatut(QueryBuilder $queryBuilder, DomSearchDto $domSearchDto)
    {
        // Filtre pour le statut        
        if (!empty($domSearchDto->statut)) {
            $queryBuilder->andWhere('s.description LIKE :statut')
                ->setParameter('statut', '%' . $domSearchDto->statut . '%');
        } else {
            $queryBuilder->andWhere('s.description NOT LIKE :excludedStatuses')
                ->setParameter('excludedStatuses', 'ANNULE%');
        }
    }

    private function filtred(QueryBuilder $queryBuilder, DomSearchDto $domSearchDto)
    {
        // Filtre pour le type de document
        if (!empty($domSearchDto->sousTypeDocument)) {
            $queryBuilder->andWhere('td.codeSousType LIKE :typeDocument')
                ->setParameter('typeDocument', '%' . $domSearchDto->sousTypeDocument . '%');
        }

        // Filtrer selon le numero DOM
        if (!empty($domSearchDto->numDom)) {
            $queryBuilder->andWhere('d.numeroOrdreMission = :numDom')
                ->setParameter('numDom', $domSearchDto->numDom);
        }

        // Filtre pour le numero matricule
        if (!empty($domSearchDto->matricule)) {
            $queryBuilder->andWhere('d.matricule = :matricule')
                ->setParameter('matricule', $domSearchDto->matricule);
        }

        // Filtre pour pièce justificatif
        if (!is_null($domSearchDto->pieceJustificatif)) {
            $queryBuilder->andWhere('d.pieceJustificatif = :pieceJustificatif')
                ->setParameter('pieceJustificatif', $domSearchDto->pieceJustificatif);
        }
    }

    private function filtredDate(QueryBuilder $queryBuilder, DomSearchDto $domSearchDto)
    {
        // Filtre pour la date de demande (début)
        if (!(empty($domSearchDto->dateDemande) && empty($domSearchDto->dateDemande['debut']))) {
            $queryBuilder->andWhere('d.dateDemande >= :dateDemandeDebut')
                ->setParameter('dateDemandeDebut', $domSearchDto->dateDemande['debut']);
        }

        // Filtre pour la date de demande (fin)
        if (!(empty($domSearchDto->dateDemande) && empty($domSearchDto->dateDemande['fin']))) {
            $queryBuilder->andWhere('d.dateDemande <= :dateDemandeFin')
                ->setParameter('dateDemandeFin', $domSearchDto->dateDemande['fin']);
        }

        // Filtre pour la date de mission (début)
        if (!(empty($domSearchDto->dateMission) && empty($domSearchDto->dateMission['debut']))) {
            $queryBuilder->andWhere('d.dateDebut >= :dateMissionDebut')
                ->setParameter('dateMissionDebut', $domSearchDto->dateMission['debut']);
        }

        // Filtre pour la date de mission (fin)
        if (!(empty($domSearchDto->dateMission) && empty($domSearchDto->dateMission['fin']))) {
            $queryBuilder->andWhere('d.dateFin <= :dateMissionFin')
                ->setParameter('dateMissionFin', $domSearchDto->dateMission['fin']);
        }
    }

    /**
     * Filtre les résultats par agences et service Emetteur et Débiteur.
     * * filtre  selon le UserAccess de l'utilisateur connecter(sécurité)
     * 
     * @param $queryBuilder Le query builder Doctrine
     */
    private function filtredAgenceService(QueryBuilder $queryBuilder, DomSearchDto $domSearchDto)
    {
        // filtre selon l'agence et service autorisées de l'utilisateur dans ContextVoter
        $this->applyDynamicContextFilter(
            $queryBuilder,
            'd',
            TypeDocumentConstants::TYPE_DOCUMENT_DOM_CODE,
            $this->getDocumentFilterConfig([], [])
        );

        // filtre selon l'agence emetteur
        if (!empty($domSearchDto->emetteur) && !empty($domSearchDto->emetteur['agence'])) {
            $queryBuilder->andWhere('d.agenceEmetteurId = :agEmet')
                ->setParameter('agEmet', $domSearchDto->emetteur['agence']->getId());
        }

        // filtre selon le service emetteur
        if (!empty($domSearchDto->emetteur) && !empty($domSearchDto->emetteur['service'])) {
            $queryBuilder->andWhere('d.serviceEmetteurId = :agServEmet')
                ->setParameter('agServEmet', $domSearchDto->emetteur['service']->getId());
        }

        // filtre selon l'agence debiteur
        if (!empty($domSearchDto->debiteur) && !empty($domSearchDto->debiteur['agence'])) {
            $queryBuilder->andWhere('d.agenceDebiteurId = :agDebit')
                ->setParameter('agDebit', $domSearchDto->debiteur['agence']->getId());
        }

        // filtre selon le service debiteur
        if (!empty($domSearchDto->debiteur) && !empty($domSearchDto->debiteur['service'])) {
            $queryBuilder->andWhere('d.serviceDebiteur = :serviceDebiteur')
                ->setParameter('serviceDebiteur', $domSearchDto->debiteur['service']->getId());
        }
    }
}

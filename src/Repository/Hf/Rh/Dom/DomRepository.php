<?php

namespace App\Repository\Hf\Rh\Dom;

use App\Entity\Hf\Rh\Dom\Dom;
use App\Dto\Hf\Rh\Dom\DomSearchDto;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Hf\Rh\Dom\SousTypeDocument;
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

    private const TYPE_MISSION_ECARTER = [
        SousTypeDocument::CODE_COMPLEMENT,
        SousTypeDocument::CODE_TROP_PERCU
    ];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dom::class);
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

    public function findPaginatedAndFiltered(int $page = 1, int $limit = 10, DomSearchDto $domSearchDto, ?array $agenceIds = null)
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

        // Créer le QueryBuilder avec les jointures et chargement eager des relations
        $queryBuilder = $this->createQueryBuilder('d')
            ->leftJoin('d.sousTypeDocument', 'td')
            ->addSelect('td')  // Évite le problème N+1
            ->leftJoin('d.idStatutDemande', 's')
            ->addSelect('s');  // Évite le problème N+1

        // 1. Filtrer par agences autorisées (sécurité)
        $this->filtredAgenceService($queryBuilder, $agenceIds);

        // 2. Appliquer les filtres de recherche
        $this->filtred($queryBuilder, $domSearchDto);
        $this->filtredDate($queryBuilder, $domSearchDto);
        $this->filtredStatut($queryBuilder, $domSearchDto);

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

    private function filtredStatut($queryBuilder, DomSearchDto $domSearchDto)
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

    private function filtred($queryBuilder, DomSearchDto $domSearchDto)
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

    private function filtredDate($queryBuilder, DomSearchDto $domSearchDto)
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
     * Filtre les résultats par agences autorisées.
     * 
     * @param $queryBuilder Le query builder Doctrine
     * @param array|null $agenceIds Les IDs des agences autorisées, null si accès à toutes
     */
    private function filtredAgenceService($queryBuilder, ?array $agenceIds): void
    {
        // Si $agenceIds est null => l'utilisateur a accès à toutes les agences (admin)
        // Sinon, filtrer uniquement par les agences autorisées
        if ($agenceIds !== null && count($agenceIds) > 0) {
            $queryBuilder->andWhere('d.agenceEmetteurId IN (:agenceIdAutoriser)')
                ->setParameter('agenceIdAutoriser', $agenceIds);
        }
    }

    /**
//     * @return Dom[] Returns an array of Dom objects
//     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('d.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Dom
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

<?php

namespace App\Repository\Hf\Materiel\Badm;

use App\Dto\Hf\Materiel\Badm\searchDto;
use App\Entity\Hf\Materiel\Badm\Badm;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

/**
 * @extends ServiceEntityRepository<Badm>
 *
 * @method Badm|null find($id, $lockMode = null, $lockVersion = null)
 * @method Badm|null findOneBy(array $criteria, array $orderBy = null)
 * @method Badm[]    findAll()
 * @method Badm[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BadmRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Badm::class);
    }

    public function add(Badm $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Badm $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Recupère le statut d'un materiel
     */
    public function getStatut(int $idMateriel): ?string
    {
        $result = $this->createQueryBuilder('b')
            ->leftJoin('b.statutDemande', 's')
            ->select('s.description')
            ->andWhere('b.idMateriel = :idMateriel')
            ->setParameter('idMateriel', $idMateriel)
            ->orderBy('b.numeroBadm', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        // Si aucun résultat, retourne null
        // Si un résultat, extrait la valeur scalaire
        return $result ? array_values($result)[0] : null;
    }

    private function sortAndLimit($searchDto, array $sortableColumns, string $defaultValue): array
    {
        // Récupérer les paramètres de tri depuis le DTO
        $sortBy = $searchDto->sortBy ?? $defaultValue;
        $sortOrder = strtoupper($searchDto->sortOrder ?? 'DESC');

        // Validation de sécurité
        if (!isset($sortableColumns[$sortBy])) {
            $sortBy = $defaultValue; // Valeur par défaut sécurisée
        }
        if (!in_array($sortOrder, ['ASC', 'DESC'])) {
            $sortOrder = 'DESC'; // Valeur par défaut sécurisée
        }

        // Récupérer la limite depuis le DTO
        $limit = $searchDto->limit ?? 50;

        return [$limit, $sortBy, $sortOrder];
    }

    public function findPaginatedAndFiltered(int $page = 1, int $limit = 10, searchDto $searchDto)
    {
        // Mapping des colonnes triables (whitelist de sécurité)
        $sortableColumns = [
            'numeroBadm' => 'b.numeroBadm'
        ];

        [$limit, $sortBy, $sortOrder] = $this->sortAndLimit($searchDto, $sortableColumns, 'numeroBadm');

        // 1. Créer le QueryBuilder avec les jointures et chargement eager des relations
        $queryBuilder = $this->createQueryBuilder('b')
            ->leftJoin('b.typeMouvement', 'tm')
            ->addSelect('tm')  // Évite le problème N+1
            ->leftJoin('b.statutDemande', 's')
            ->addSelect('s');  // Évite le problème N+1

        // TODO : 2. Appliquer les filtres de recherche
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
}

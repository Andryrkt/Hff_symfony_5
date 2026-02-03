<?php

namespace App\Repository\Hf\Atelier\Dit;

use App\Entity\Hf\Atelier\Dit\Dit;
use App\Contract\Dto\SearchDtoInterface;
use App\Repository\Traits\PaginatableRepositoryTrait;
use App\Contract\Repository\PaginatedRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Dit>
 *
 * @method Dit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dit[]    findAll()
 * @method Dit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DitRepository extends ServiceEntityRepository implements PaginatedRepositoryInterface
{
    use PaginatableRepositoryTrait;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dit::class);
    }

    public function add(Dit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Dit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param int $page
     * @param int $limit
     * @param SearchDtoInterface $searchDto
     * @return array
     */
    public function findPaginatedAndFiltered(int $page, int $limit, SearchDtoInterface $searchDto): array
    {
        $sortableColumns = [
            'numeroDit' => 'd.numeroDit',
            'dateDemande' => 'd.createdAt',
        ];

        [$limit, $sortBy, $sortOrder] = $this->sortAndLimit($searchDto, $sortableColumns, 'numeroDit');

        $queryBuilder = $this->createQueryBuilder('d')
            ->leftJoin('d.worNiveauUrgence', 'wn')
            ->addSelect('wn')
            ->leftJoin('d.worTypeDocument', 'wd')
            ->addSelect('wd')
            ->leftJoin('d.statutDemande', 's')
            ->addSelect('s');

        // TODO: Appliquer les filtres spécifiques ici si nécessaire

        $queryBuilder->orderBy($sortableColumns[$sortBy], $sortOrder)
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        $paginator = new DoctrinePaginator($queryBuilder);
        $totalItems = count($paginator);

        return [
            'data'        => iterator_to_array($paginator->getIterator()),
            'totalItems'  => $totalItems,
            'currentPage' => $page,
            'lastPage'    => (int) ceil($totalItems / $limit),
        ];
    }

    //    /**
    //     * @return Dit[] Returns an array of Dit objects
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

    //    public function findOneBySomeField($value): ?Dit
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

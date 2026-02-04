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

    /** DIT SEARCH section DEBUT  */
    public function findSectionSupport1()
    {
        $result = $this->createQueryBuilder('d')
            ->select('DISTINCT d.sectionSupport1')
            ->where('d.sectionAffectee IS NOT NULL')
            ->andWhere('d.sectionAffectee != :sectionAffectee')
            ->setParameter('sectionAffectee', ' ')
            ->andWhere('d.sectionAffectee != :sectionAffecte')
            ->setParameter('sectionAffecte', 'Autres')
            ->getQuery()
            ->getScalarResult();
        return array_column($result, 'sectionSupport1');
    }

    public function findSectionSupport2()
    {
        $result = $this->createQueryBuilder('d')
            ->select('DISTINCT d.sectionSupport2')
            ->where('d.sectionAffectee IS NOT NULL')
            ->andWhere('d.sectionAffectee != :sectionAffectee')
            ->setParameter('sectionAffectee', ' ')
            ->andWhere('d.sectionAffectee != :sectionAffecte')
            ->setParameter('sectionAffecte', 'Autres')
            ->getQuery()
            ->getScalarResult();
        return array_column($result, 'sectionSupport2');
    }

    public function findSectionSupport3()
    {
        $result = $this->createQueryBuilder('d')
            ->select('DISTINCT d.sectionSupport3')
            ->where('d.sectionAffectee IS NOT NULL')
            ->andWhere('d.sectionAffectee != :sectionAffectee')
            ->setParameter('sectionAffectee', ' ')
            ->andWhere('d.sectionAffectee != :sectionAffecte')
            ->setParameter('sectionAffecte', 'Autres')
            ->getQuery()
            ->getScalarResult();
        return array_column($result, 'sectionSupport3');
    }

    public function findSectionAffectee()
    {
        $result = $this->createQueryBuilder('d')
            ->select('DISTINCT d.sectionAffectee')
            ->where('d.sectionAffectee IS NOT NULL')
            ->andWhere('d.sectionAffectee != :sectionAffectee')
            ->setParameter('sectionAffectee', ' ')
            ->andWhere('d.sectionAffectee != :sectionAffecte')
            ->setParameter('sectionAffecte', 'Autres')
            ->getQuery()
            ->getScalarResult();
        return array_column($result, 'sectionAffectee');
    }

    public function findStatutOr()
    {
        $result = $this->createQueryBuilder('d')
            ->select('DISTINCT d.statutOr')
            ->where('d.statutOr IS NOT NULL')
            ->getQuery()
            ->getScalarResult();
        return array_column($result, 'statutOr');
    }
    /** DIT SEARCH section FIN  */
}

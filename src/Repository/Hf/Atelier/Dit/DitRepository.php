<?php

namespace App\Repository\Hf\Atelier\Dit;

use App\Entity\Hf\Atelier\Dit\Dit;
use App\Contract\Dto\SearchDtoInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\Traits\PaginatableRepositoryTrait;
use App\Contract\Repository\PaginatedRepositoryInterface;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Service\Hf\Atelier\Dit\DitSearchFilter;

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

    private $searchFilter;

    public function __construct(ManagerRegistry $registry, DitSearchFilter $searchFilter)
    {
        parent::__construct($registry, Dit::class);
        $this->searchFilter = $searchFilter;
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
     * Recupérer les informations à afficher sur la liste selon les filtres
     * 
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

        // Appliquer les filtres via le service dédié
        $this->searchFilter->applyFilters($queryBuilder, $searchDto);

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

    public function findFilteredExcel(SearchDtoInterface $searchDto): array
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

        // 2. Appliquer les filtres de recherche
        // $this->searchFilter->applyFilters($queryBuilder, $searchDto);

        // 3. Ordre
        $queryBuilder->orderBy($sortableColumns[$sortBy], $sortOrder);

        return $queryBuilder->getQuery()->getResult();
    }

    /** DIT section DEBUT  */
    public function findSectionSupport1(): array
    {
        return $this->findDistinctValues('sectionSupport1');
    }

    public function findSectionSupport2(): array
    {
        return $this->findDistinctValues('sectionSupport2');
    }

    public function findSectionSupport3(): array
    {
        return $this->findDistinctValues('sectionSupport3');
    }

    public function findSectionAffectee(): array
    {
        return $this->findDistinctValues('sectionAffectee');
    }

    public function findStatutOr(): array
    {
        $result = $this->createQueryBuilder('d')
            ->select('DISTINCT d.statutOr')
            ->where('d.statutOr IS NOT NULL')
            ->getQuery()
            ->getScalarResult();
        return array_column($result, 'statutOr');
    }

    /**
     * Méthode générique pour récupérer les valeurs distinctes d'un champ
     */
    private function findDistinctValues(string $field): array
    {
        $result = $this->createQueryBuilder('d')
            ->select("DISTINCT d.$field")
            ->where("d.$field IS NOT NULL")
            ->andWhere("d.sectionAffectee IS NOT NULL")
            ->andWhere("d.sectionAffectee != ' '")
            ->andWhere("d.sectionAffectee != 'Autres'")
            ->getQuery()
            ->getScalarResult();

        return array_column($result, $field);
    }
    /** DIT section FIN  */

    /** DIT A ANNULER DEBUT */
    public function getNumDitAAnnuler()
    {
        $dateNow = new \DateTime(); // maintenant
        $dateYesterday = (clone $dateNow)->modify('-1 day'); // 1 jour avant

        return $this->createQueryBuilder('d')
            ->select('d.numeroDemandeIntervention')
            ->where('d.aAnnuler = :aAnnuler')
            ->andWhere('d.dateAnnulation BETWEEN :yesterday AND :now')
            ->setParameters([
                'aAnnuler' => 1,
                'yesterday' => $dateYesterday,
                'now' => $dateNow,
            ])
            ->getQuery()
            ->getSingleColumnResult()
        ;
    }
    /** DIT A ANNULER FIN */
}

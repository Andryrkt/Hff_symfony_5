<?php

namespace App\Repository\Hf\Materiel\Casier;

use App\Dto\Hf\Materiel\Casier\SearchDto;
use App\Entity\Hf\Materiel\Casier\Casier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CasierPhp>
 *
 * @method CasierPhp|null find($id, $lockMode = null, $lockVersion = null)
 * @method CasierPhp|null findOneBy(array $criteria, array $orderBy = null)
 * @method CasierPhp[]    findAll()
 * @method CasierPhp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CasierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Casier::class);
    }

    public function add(Casier $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Casier $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getCasiersTemporaire(SearchDto $searchDto)
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->orderBy('c.numero', 'DESC');

        $this->filtred($searchDto, $queryBuilder);

        return $queryBuilder->getQuery()->getResult();
    }

    private function filtred(SearchDto $searchDto, QueryBuilder $queryBuilder)
    {
        if (!empty($searchDto->agence)) {
            $queryBuilder->andWhere('c.agenceRattacher = :agence')
                ->setParameter('agence', $searchDto->agence->getId());
        }
        if (!empty($searchDto->casier)) {
            $queryBuilder->andWhere('c.nom LIKE :casier')
                ->setParameter('casier', '%' . $searchDto->casier . '%');
        }

        if (!empty($searchDto->statut)) {
            $queryBuilder->andWhere('c.statutDemande = :statut')
                ->setParameter('statut', $searchDto->statut->getId());
        }
    }

    //    /**
    //     * @return CasierPhp[] Returns an array of CasierPhp objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?CasierPhp
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

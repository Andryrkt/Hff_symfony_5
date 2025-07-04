<?php

namespace App\Repository\Admin\AgenceService;

use App\Entity\Admin\AgenceService\AgenceServiceIrium;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AgenceServiceIrium>
 *
 * @method AgenceServiceIrium|null find($id, $lockMode = null, $lockVersion = null)
 * @method AgenceServiceIrium|null findOneBy(array $criteria, array $orderBy = null)
 * @method AgenceServiceIrium[]    findAll()
 * @method AgenceServiceIrium[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AgenceServiceIriumRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AgenceServiceIrium::class);
    }

    public function add(AgenceServiceIrium $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AgenceServiceIrium $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return AgenceServiceIrium[] Returns an array of AgenceServiceIrium objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?AgenceServiceIrium
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

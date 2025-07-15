<?php

namespace App\Repository\Dom;

use App\Entity\Dom\DemandeOrdreMission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DemandeOrdreMission>
 *
 * @method DemandeOrdreMission|null find($id, $lockMode = null, $lockVersion = null)
 * @method DemandeOrdreMission|null findOneBy(array $criteria, array $orderBy = null)
 * @method DemandeOrdreMission[]    findAll()
 * @method DemandeOrdreMission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DemandeOrdreMissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DemandeOrdreMission::class);
    }

    public function add(DemandeOrdreMission $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DemandeOrdreMission $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return DemandeOrdreMission[] Returns an array of DemandeOrdreMission objects
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

//    public function findOneBySomeField($value): ?DemandeOrdreMission
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

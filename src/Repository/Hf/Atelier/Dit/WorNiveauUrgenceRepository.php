<?php

namespace App\Repository\Hf\Atelier\Dit;

use App\Entity\Hf\Atelier\Dit\WorNiveauUrgence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WorNiveauUrgence>
 *
 * @method WorNiveauUrgence|null find($id, $lockMode = null, $lockVersion = null)
 * @method WorNiveauUrgence|null findOneBy(array $criteria, array $orderBy = null)
 * @method WorNiveauUrgence[]    findAll()
 * @method WorNiveauUrgence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorNiveauUrgenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorNiveauUrgence::class);
    }

    public function add(WorNiveauUrgence $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(WorNiveauUrgence $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return WorNiveauUrgence[] Returns an array of WorNiveauUrgence objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('w.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?WorNiveauUrgence
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

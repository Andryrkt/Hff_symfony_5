<?php

namespace App\Repository\Admin\ApplicationGroupe;

use App\Entity\Admin\ApplicationGroupe\SequenceAppllication;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SequenceAppllication>
 *
 * @method SequenceAppllication|null find($id, $lockMode = null, $lockVersion = null)
 * @method SequenceAppllication|null findOneBy(array $criteria, array $orderBy = null)
 * @method SequenceAppllication[]    findAll()
 * @method SequenceAppllication[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SequenceAppllicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SequenceAppllication::class);
    }

    public function add(SequenceAppllication $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SequenceAppllication $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneByCodeAppWithLock(string $codeApp): ?SequenceAppllication
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.codeApp = :codeApp')
            ->setParameter('codeApp', $codeApp)
            ->getQuery()
            ->setLockMode(\Doctrine\DBAL\LockMode::PESSIMISTIC_WRITE)
            ->getOneOrNullResult();
    }

//    /**
//     * @return SequenceAppllication[] Returns an array of SequenceAppllication objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SequenceAppllication
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

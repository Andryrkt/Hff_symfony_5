<?php

namespace App\Repository\Hf\Atelier\Dit;

use App\Entity\Hf\Atelier\Dit\WorTypeDocument;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WorTypeDocument>
 *
 * @method WorTypeDocument|null find($id, $lockMode = null, $lockVersion = null)
 * @method WorTypeDocument|null findOneBy(array $criteria, array $orderBy = null)
 * @method WorTypeDocument[]    findAll()
 * @method WorTypeDocument[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorTypeDocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorTypeDocument::class);
    }

    public function add(WorTypeDocument $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(WorTypeDocument $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return WorTypeDocument[] Returns an array of WorTypeDocument objects
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

//    public function findOneBySomeField($value): ?WorTypeDocument
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

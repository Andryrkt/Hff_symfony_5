<?php

namespace App\Repository\Hf\Rh\Dom;


use App\Entity\Hf\Rh\Dom\SousTypeDocument;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<SousTypeDocument>
 *
 * @method SousTypeDocument|null find($id, $lockMode = null, $lockVersion = null)
 * @method SousTypeDocument|null findOneBy(array $criteria, array $orderBy = null)
 * @method SousTypeDocument[]    findAll()
 * @method SousTypeDocument[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SousTypeDocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SousTypeDocument::class);
    }

    public function add(SousTypeDocument $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SousTypeDocument $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return SousTypeDocument[] Returns an array of SousTypeDocument objects
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

//    public function findOneBySomeField($value): ?SousTypeDocument
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

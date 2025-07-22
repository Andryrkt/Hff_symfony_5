<?php

namespace App\Repository\Dom;

use App\Entity\Dom\DomSousTypeDocument;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DomSousTypeDocument>
 *
 * @method DomSousTypeDocument|null find($id, $lockMode = null, $lockVersion = null)
 * @method DomSousTypeDocument|null findOneBy(array $criteria, array $orderBy = null)
 * @method DomSousTypeDocument[]    findAll()
 * @method DomSousTypeDocument[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DomSousTypeDocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DomSousTypeDocument::class);
    }

    public function add(DomSousTypeDocument $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DomSousTypeDocument $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return DomSousTypeDocument[] Returns an array of DomSousTypeDocument objects
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

    //    public function findOneBySomeField($value): ?DomSousTypeDocument
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

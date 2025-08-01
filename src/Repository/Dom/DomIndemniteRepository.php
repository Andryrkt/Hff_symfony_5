<?php

namespace App\Repository\Dom;

use App\Entity\Dom\DomIndemnite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DomIndemnite>
 *
 * @method DomIndemnite|null find($id, $lockMode = null, $lockVersion = null)
 * @method DomIndemnite|null findOneBy(array $criteria, array $orderBy = null)
 * @method DomIndemnite[]    findAll()
 * @method DomIndemnite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DomIndemniteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DomIndemnite::class);
    }

    public function add(DomIndemnite $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DomIndemnite $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findDistinctCategoriesByCriteria(array $criteria): array
    {
        $dql = <<<DQL
        SELECT DISTINCT c
        FROM App\Entity\Dom\DomCategorie c
        JOIN c.domIndemnites i
        WHERE i.domSousTypeDocumentId = :sousTypeDoc
        AND i.domRmqId = :rmq
    DQL;

        return $this->getEntityManager()->createQuery($dql)
            ->setParameters([
                'sousTypeDoc' => $criteria['sousTypeDoc'],
                'rmq' => $criteria['rmq'],
            ])
            ->getResult();
    }

    public function findIdemnite(array $criteria)
    {
        return  $this->createQueryBuilder('i')
            ->where('i.sousTypeDoc = :sousTypeDoc')
            ->andWhere('i.rmq = :rmq')
            ->andWhere('i.categorie = :categorie')
            ->setParameters([
                'sousTypeDoc' => $criteria['sousTypeDoc'],
                'rmq' => $criteria['rmq'],
                'categorie' => $criteria['categorie']
            ])
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return DomIndemnite[] Returns an array of DomIndemnite objects
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

    //    public function findOneBySomeField($value): ?DomIndemnite
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

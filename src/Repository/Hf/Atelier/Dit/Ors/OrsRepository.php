<?php

namespace App\Repository\Hf\Atelier\Dit\Ors;

use App\Entity\Hf\Atelier\Dit\Ors\Ors;
use App\Service\Traits\ArrayHelperTrait;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Ors>
 *
 * @method Ors|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ors|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ors[]    findAll()
 * @method Ors[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrsRepository extends ServiceEntityRepository
{
    use ArrayHelperTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ors::class);
    }

    public function add(Ors $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Ors $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllOrsValide()
    {
        $qb = $this->createQueryBuilder('o');

        // Subquery to check if 'o' is the latest version
        $subQb = $this->getEntityManager()->createQueryBuilder()
            ->select('MAX(oo.numeroVersion)')
            ->from(Ors::class, 'oo')
            ->where('oo.numeroOr = o.numeroOr');

        $qb->select("DISTINCT CONCAT(o.numeroOr, '-', o.numeroItv) as numero_or_itv", "o.numeroOr as numero_or")
            ->innerJoin('App\Entity\Hf\Atelier\Dit\Dit', 'd', 'WITH', 'd.numeroOr = o.numeroOr')
            ->andWhere($qb->expr()->eq('o.numeroVersion', '(' . $subQb->getDQL() . ')'))
            ->andWhere("o.statut LIKE :statut")
            ->setParameter('statut', 'Valid%');

        return $qb->getQuery()->getResult();
    }

    public function findAllOrsString(): string
    {
        $qb = $this->createQueryBuilder('o');
        $qb->select("DISTINCT o.numeroOr");

        $result = $qb->getQuery()->getResult();

        return $this->TableauEnString($result);
    }

    public function findAllOrsWithItvString(): string
    {
        $qb = $this->createQueryBuilder('o');
        $qb->select("DISTINCT CONCAT(o.numeroOr, '-', o.numeroItv) as numero_or_itv");

        $result = $qb->getQuery()->getResult();

        return $this->TableauEnString($result);
    }

    //    /**
    //     * @return Ors[] Returns an array of Ors objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('o.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Ors
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

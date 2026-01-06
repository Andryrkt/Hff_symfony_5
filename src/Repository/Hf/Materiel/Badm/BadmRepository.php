<?php

namespace App\Repository\Hf\Materiel\Badm;

use App\Entity\Hf\Materiel\Badm\Badm;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Badm>
 *
 * @method Badm|null find($id, $lockMode = null, $lockVersion = null)
 * @method Badm|null findOneBy(array $criteria, array $orderBy = null)
 * @method Badm[]    findAll()
 * @method Badm[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BadmRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Badm::class);
    }

    public function add(Badm $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Badm $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Recupère le statut d'un materiel
     */
    public function getStatut(int $idMateriel): ?string
    {
        $result = $this->createQueryBuilder('b')
            ->leftJoin('b.statutDemande', 's')
            ->select('s.description')
            ->andWhere('b.idMateriel = :idMateriel')
            ->setParameter('idMateriel', $idMateriel)
            ->orderBy('b.numeroBadm', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        // Si aucun résultat, retourne null
        // Si un résultat, extrait la valeur scalaire
        return $result ? array_values($result)[0] : null;
    }
    //    /**
    //     * @return Badm[] Returns an array of Badm objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Badm
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

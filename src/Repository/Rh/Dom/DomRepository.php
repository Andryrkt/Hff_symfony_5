<?php

namespace App\Repository\Rh\Dom;

use App\Entity\Rh\Dom\Dom;
use App\Entity\Rh\Dom\SousTypeDocument;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Dom>
 *
 * @method Dom|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dom|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dom[]    findAll()
 * @method Dom[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DomRepository extends ServiceEntityRepository
{

    private const TYPE_MISSION_ECARTER = [
        SousTypeDocument::CODE_COMPLEMENT,
        SousTypeDocument::CODE_TROP_PERCU
    ];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dom::class);
    }

    public function add(Dom $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Dom $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    /**
     * Vérifie si une mission existe déjà pour un matricule donné sur une période qui se chevauche.
     */
    public function hasOverlappingMission(
        string $matricule,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate
    ): bool {
        // Adaptez 'm.personnel' et 'p.matricule' si vos relations/propriétés sont différentes
        // Adaptez 'm.dateDebut' et 'm.dateFin' aux noms de vos champs de date dans l'entité Mission
        $qb = $this->createQueryBuilder('m')
            ->select('count(m.id)')
            ->innerJoin('m.sousTypeDocument', 't')
            ->where('m.matricule = :matricule')
            ->andWhere('t.codeSousType NOT IN (:typeMission)')
            // La nouvelle mission ne doit pas commencer pendant une mission existante
            // ET la nouvelle mission ne doit pas se terminer pendant une mission existante
            // ET la nouvelle mission ne doit pas englober une mission existante
            ->andWhere('m.dateDebut < :endDate AND m.dateFin > :startDate')
            ->setParameters([
                'matricule' => $matricule,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'typeMission' => self::TYPE_MISSION_ECARTER
            ]);

        $count = $qb->getQuery()->getSingleScalarResult();

        return $count > 0;
    }

    public function getInfoDOMMatrSelet(string $matricule)
    {
        return $this->createQueryBuilder('d')
            ->select('d.dateDebut, d.dateFin')
            ->where('d.matricule = :matricule')
            ->andWhere('d.idStatutDemande not in (:excludStatut)')
            ->setParameters([
                'matricule' => $matricule,
                'excludStatut' => []
            ])
            ->getQuery()
            ->getArrayResult()
        ;
    }


    /**
//     * @return Dom[] Returns an array of Dom objects
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

    //    public function findOneBySomeField($value): ?Dom
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

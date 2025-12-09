<?php

namespace App\Repository\Admin\PersonnelUser;

use App\Entity\Admin\PersonnelUser\Personnel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Personnel>
 *
 * @method Personnel|null find($id, $lockMode = null, $lockVersion = null)
 * @method Personnel|null findOneBy(array $criteria, array $orderBy = null)
 * @method Personnel[]    findAll()
 * @method Personnel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonnelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Personnel::class);
    }

    public function add(Personnel $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Personnel $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return Personnel[] Returns an array of Personnel objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Personnel
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    /**
     * Retourne un tableau [Label => ID] pour le champ ChoiceType
     * Optimisé pour éviter l'hydratation de milliers d'objets.
     */
    public function findChoicesForUser($user): array
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.agenceServiceIrium', 'asi')
            ->leftJoin('asi.agence', 'a')
            ->leftJoin('asi.service', 's')
            ->select('p.id, p.matricule, p.nom, p.prenoms')
            ->orderBy('p.matricule', 'ASC');

        // Filtrage selon les droits de l'utilisateur
        if ($user && method_exists($user, 'getUserAccesses')) {
            $userAccesses = $user->getUserAccesses();
            $agenceIds = [];
            $serviceIds = [];
            $allAgence = false;
            $allService = false;

            foreach ($userAccesses as $userAccess) {
                if ($userAccess->getAllAgence()) {
                    $allAgence = true;
                } elseif ($userAccess->getAgence()) {
                    $agenceIds[] = $userAccess->getAgence()->getId();
                }

                if ($userAccess->getAllService()) {
                    $allService = true;
                } elseif ($userAccess->getService()) {
                    $serviceIds[] = $userAccess->getService()->getId();
                }
            }

            // Si pas accès à toutes les agences, on filtre par les IDs autorisés
            // On utilise 'a.id' (l'agence liée via agenceServiceIrium)
            if (!$allAgence && !empty($agenceIds)) {
                $qb->andWhere('a.id IN (:agenceIds)')
                    ->setParameter('agenceIds', $agenceIds);
            }

            // Si pas accès à tous les services
            if (!$allService && !empty($serviceIds)) {
                $qb->andWhere('s.id IN (:serviceIds)')
                    ->setParameter('serviceIds', $serviceIds);
            }
        }

        $results = $qb->getQuery()->getArrayResult();
        $choices = [];

        foreach ($results as $row) {
            $label = $row['matricule'] . ' ' . $row['nom'] . ' ' . $row['prenoms'];
            // ChoiceType attend [Label => Value] ou juste les valeurs si choice_label est utilisé
            // Mais ici on prépare le tableau final [Label => ID]
            $choices[$label] = $row['id'];
        }

        return $choices;
    }
}

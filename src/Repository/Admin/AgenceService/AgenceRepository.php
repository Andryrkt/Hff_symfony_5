<?php

namespace App\Repository\Admin\AgenceService;

use App\Entity\Admin\AgenceService\Agence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Agence>
 *
 * @method Agence|null find($id, $lockMode = null, $lockVersion = null)
 * @method Agence|null findOneBy(array $criteria, array $orderBy = null)
 * @method Agence[]    findAll()
 * @method Agence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AgenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Agence::class);
    }

    public function add(Agence $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Agence $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Récupère toutes les agences avec leurs services pré-chargés (Eager Loading)
     * pour éviter le problème N+1.
     *
     * @return Agence[]
     */
    public function findAllWithServices(): array
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.services', 's')
            ->addSelect('s')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère toutes les agences avec leurs services au format tableau optimisé
     * pour les dropdowns. Cette méthode est beaucoup plus rapide que la sérialisation
     * d'objets Doctrine car elle retourne directement un tableau.
     *
     * @return array Format: [['id' => 1, 'code' => '01', 'nom' => 'ANTANANARIVO', 'services' => [...]]]
     */
    public function findAllForDropdownOptimized(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        // Requête SQL optimisée qui récupère tout en une seule requête
        $sql = '
            SELECT 
                a.id as agence_id,
                a.code as agence_code,
                a.nom as agence_nom,
                s.id as service_id,
                s.code as service_code,
                s.nom as service_nom
            FROM agence a
            LEFT JOIN agence_service ags ON a.id = ags.agence_id
            LEFT JOIN service s ON ags.service_id = s.id
            ORDER BY a.code ASC, s.code ASC
        ';

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery();
        $rows = $result->fetchAllAssociative();

        // Regrouper les services par agence
        $agences = [];
        foreach ($rows as $row) {
            $agenceId = $row['agence_id'];

            if (!isset($agences[$agenceId])) {
                $agences[$agenceId] = [
                    'id' => $agenceId,
                    'code' => $row['agence_code'],
                    'nom' => $row['agence_nom'],
                    'services' => []
                ];
            }

            // Ajouter le service si présent
            if ($row['service_id'] !== null) {
                $agences[$agenceId]['services'][] = [
                    'id' => $row['service_id'],
                    'code' => $row['service_code'],
                    'nom' => $row['service_nom']
                ];
            }
        }

        return array_values($agences);
    }

    //    /**
    //     * @return Agence[] Returns an array of Agence objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Agence
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

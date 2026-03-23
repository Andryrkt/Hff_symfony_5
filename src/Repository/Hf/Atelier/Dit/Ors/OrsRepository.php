<?php

namespace App\Repository\Hf\Atelier\Dit\Ors;


use App\Entity\Hf\Atelier\Dit\Soumission\Ors\Ors;
use App\Service\Traits\ArrayHelperTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

    public function add(array $entities, bool $flush = false): void
    {
        foreach ($entities as $entity) {
            $this->getEntityManager()->persist($entity);
        }

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

    public function getNumeroVersion(string $numeroOr): ?int
    {
        $qb = $this->createQueryBuilder('o');
        $qb->select('MAX(o.numeroVersion)')
            ->where('o.numeroOr = :numeroOr')
            ->setParameter('numeroOr', $numeroOr);

        return $qb->getQuery()->getSingleScalarResult();
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

    /**
     * Récupère tous les OR avec leur ITV sous forme de string (numeroOr-numeroItv)
     * @return string
     */
    public function findAllOrsWithItvString(): string
    {
        $qb = $this->createQueryBuilder('o');
        $qb->select("DISTINCT CONCAT(o.numeroOr, '-', o.numeroItv) as numero_or_itv");

        $result = $qb->getQuery()->getResult();

        return $this->TableauEnString($result);
    }

    /**
     * @return Ors[]
     */
    public function findByOrAndVersion(string $numeroOr, int $numeroVersion): array
    {
        return $this->findBy([
            'numeroOr' => $numeroOr,
            'numeroVersion' => $numeroVersion
        ]);
    }
}

<?php

namespace App\Repository\Hf\Materiel\Badm;

use App\Contract\Repository\PaginatedRepositoryInterface;
use App\Contract\Dto\SearchDtoInterface;
use Doctrine\ORM\QueryBuilder;
use App\Entity\Hf\Materiel\Badm\Badm;
use App\Dto\Hf\Materiel\Badm\searchDto;
use App\Model\Hf\Materiel\Badm\BadmModel;
use Doctrine\Persistence\ManagerRegistry;
use App\Constants\Admin\Historisation\TypeDocumentConstants;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use App\Repository\Traits\PaginatableRepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Badm>
 *
 * @method Badm|null find($id, $lockMode = null, $lockVersion = null)
 * @method Badm|null findOneBy(array $criteria, array $orderBy = null)
 * @method Badm[]    findAll()
 * @method Badm[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BadmRepository extends ServiceEntityRepository implements PaginatedRepositoryInterface
{
    use PaginatableRepositoryTrait;

    private BadmModel $badmModel;

    public function __construct(ManagerRegistry $registry, BadmModel $badmModel)
    {
        parent::__construct($registry, Badm::class);
        $this->badmModel = $badmModel;
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


    /**
     * @param int $page
     * @param int $limit
     * @param SearchDtoInterface|searchDto $searchDto
     * @return array
     */
    public function findPaginatedAndFiltered(int $page, int $limit, SearchDtoInterface $searchDto): array
    {
        // Mapping des colonnes triables (whitelist de sécurité)
        $sortableColumns = [
            'numeroBadm' => 'b.numeroBadm',
            'dateDemande' => 'b.createdAt',
            'typeMouvement' => 'tm.description',
            'statut' => 's.description',
        ];

        [$limit, $sortBy, $sortOrder] = $this->sortAndLimit($searchDto, $sortableColumns, 'numeroBadm');

        // 1. Créer le QueryBuilder avec les jointures et chargement eager des relations
        $queryBuilder = $this->createQueryBuilder('b')
            ->leftJoin('b.typeMouvement', 'tm')
            ->addSelect('tm')  // Évite le problème N+1
            ->leftJoin('b.statutDemande', 's')
            ->addSelect('s');  // Évite le problème N+1

        // 2. Appliquer les filtres de recherche
        $this->filtredDate($queryBuilder, $searchDto);
        $this->filtredAgenceService($queryBuilder, $searchDto);
        $this->filtredStatut($queryBuilder, $searchDto);
        $this->filtredIdentiteMateriel($queryBuilder, $searchDto);
        $this->filtred($queryBuilder, $searchDto);

        // 3. Ordre et pagination
        $queryBuilder->orderBy($sortableColumns[$sortBy], $sortOrder)
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        // 4. Exécuter avec Paginator
        $paginator = new DoctrinePaginator($queryBuilder);
        $totalItems = count($paginator);
        $lastPage = ceil($totalItems / $limit);

        return [
            'data'        => iterator_to_array($paginator->getIterator()),
            'totalItems'  => $totalItems,
            'currentPage' => $page,
            'lastPage'    => $lastPage,
        ];
    }

    public function findFilteredExcel(SearchDto $searchDto)
    {
        $sortableColumns = [
            'numeroBadm' => 'b.numeroBadm',
            'dateDemande' => 'b.createdAt',
            'typeMouvement' => 'tm.description',
            'statut' => 's.description',
        ];

        [$limit, $sortBy, $sortOrder] = $this->sortAndLimit($searchDto, $sortableColumns, 'numeroBadm');

        // 1. Créer le QueryBuilder avec les jointures et chargement eager des relations
        $queryBuilder = $this->createQueryBuilder('b')
            ->leftJoin('b.typeMouvement', 'tm')
            ->addSelect('tm')  // Évite le problème N+1
            ->leftJoin('b.statutDemande', 's')
            ->addSelect('s');  // Évite le problème N+1

        // 2. Appliquer les filtres de recherche
        $this->filtredDate($queryBuilder, $searchDto);
        $this->filtredAgenceService($queryBuilder, $searchDto);
        $this->filtredStatut($queryBuilder, $searchDto);
        $this->filtredIdentiteMateriel($queryBuilder, $searchDto);
        $this->filtred($queryBuilder, $searchDto);

        // 3. Ordre
        $queryBuilder->orderBy($sortableColumns[$sortBy], $sortOrder);


        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Filtre les résultats par date de demande
     */
    private function filtredDate(QueryBuilder $queryBuilder, SearchDto $searchDto): void
    {
        // Filtre pour la date de demande (début)
        if (!(empty($searchDto->dateDemande) && empty($searchDto->dateDemande['debut']))) {
            $queryBuilder->andWhere('b.createdAt >= :dateDemandeDebut')
                ->setParameter('dateDemandeDebut', $searchDto->dateDemande['debut']);
        }

        // Filtre pour la date de demande (fin)
        if (!(empty($searchDto->dateDemande) && empty($searchDto->dateDemande['fin']))) {
            $queryBuilder->andWhere('b.createdAt <= :dateDemandeFin')
                ->setParameter('dateDemandeFin', $searchDto->dateDemande['fin']);
        }
    }

    /**
     * Filtre les résultats par agences et service Emetteur et Débiteur.
     * * filtre  selon le UserAccess de l'utilisateur connecter(sécurité)
     * 
     * @param $queryBuilder Le query builder Doctrine
     */
    private function filtredAgenceService(QueryBuilder $queryBuilder, SearchDto $searchDto): void
    {
        // TODO: filtre selon l'agence et service autorisées de l'utilisateur dans ContextVoter
        // $this->applyDynamicContextFilter(
        //     $queryBuilder,
        //     'b',
        //     TypeDocumentConstants::TYPE_DOCUMENT_BADM_CODE,
        //     $this->getDocumentFilterConfig(['agenceDebiteurId'])
        // );

        // filtre selon l'agence emetteur
        if (!empty($searchDto->emetteur) && !empty($searchDto->emetteur['agence'])) {
            $queryBuilder->andWhere('b.agenceEmetteurId = :agEmet')
                ->setParameter('agEmet', $searchDto->emetteur['agence']->getId());
        }

        // filtre selon le service emetteur
        if (!empty($searchDto->emetteur) && !empty($searchDto->emetteur['service'])) {
            $queryBuilder->andWhere('b.serviceEmetteurId = :agServEmet')
                ->setParameter('agServEmet', $searchDto->emetteur['service']->getId());
        }

        // filtre selon l'agence debiteur
        if (!empty($searchDto->debiteur) && !empty($searchDto->debiteur['agence'])) {
            $queryBuilder->andWhere('b.agenceDebiteurId = :agDebit')
                ->setParameter('agDebit', $searchDto->debiteur['agence']->getId());
        }

        // filtre selon le service debiteur
        if (!empty($searchDto->debiteur) && !empty($searchDto->debiteur['service'])) {
            $queryBuilder->andWhere('b.serviceDebiteur = :serviceDebiteur')
                ->setParameter('serviceDebiteur', $searchDto->debiteur['service']->getId());
        }
    }

    /**
     * Filtre pour le statut
     * *on n'affiche pas les BADM qui a le statut annulé sauf si on le recherche
     */
    private function filtredStatut(QueryBuilder $queryBuilder, SearchDto $searchDto): void
    {
        // Filtre pour le statut        
        if (!empty($searchDto->statut)) {
            $queryBuilder->andWhere('s.description LIKE :statut')
                ->setParameter('statut', '%' . $searchDto->statut->getDescription() . '%');
        } else {
            $queryBuilder->andWhere('s.description NOT LIKE :excludedStatuses')
                ->setParameter('excludedStatuses', 'ANNULE%');
        }
    }

    private function filtredIdentiteMateriel(QueryBuilder $queryBuilder, SearchDto $searchDto): void
    {
        // Filtre pour l'Id matériel
        if (!empty($searchDto->idMateriel)) {
            $queryBuilder->andWhere('b.idMateriel = :matricule')
                ->setParameter('matricule', $searchDto->idMateriel);
        }

        // Filtre pour le numero Parc
        if (!empty($searchDto->numParc)) {
            $queryBuilder->andWhere('b.numParc = :numParc')
                ->setParameter('numParc', $searchDto->numParc);
        }

        // Filtre pour le numero Serie à partir du l'id materiel
        if (!empty($searchDto->numSerie)) {
            $numSerieDesignation = $this->badmModel->getNumSerieDesignationMateriel($searchDto);
            if ($numSerieDesignation) {
                $queryBuilder->andWhere('b.idMateriel = :matricule')
                    ->setParameter('matricule', $numSerieDesignation['num_matricule']);
            }
        }
    }

    private function filtred(QueryBuilder $queryBuilder, SearchDto $searchDto): void
    {
        // Filtre pour le type de mouvement
        if (!empty($searchDto->typeMouvement)) {
            $queryBuilder->andWhere('tm.description LIKE :typeMouvement')
                ->setParameter('typeMouvement', '%' . $searchDto->typeMouvement->getDescription() . '%');
        }

        // Filtrer selon le numero DOM
        if (!empty($searchDto->numeroBadm)) {
            $queryBuilder->andWhere('b.numeroBadm = :numBadm')
                ->setParameter('numBadm', $searchDto->numeroBadm);
        }
    }
}

<?php

namespace App\Service\Dom;

use App\Entity\Dom\DomSousTypeDocument;
use App\Entity\Dom\DomCategorie;
use App\Entity\Dom\DomRmq;
use App\Repository\Dom\DomSousTypeDocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class DomCacheService
{
    private CacheInterface $cache;
    private EntityManagerInterface $em;
    private DomSousTypeDocumentRepository $sousTypeDocumentRepository;

    public function __construct(
        CacheInterface $cache,
        EntityManagerInterface $em,
        DomSousTypeDocumentRepository $sousTypeDocumentRepository
    ) {
        $this->cache = $cache;
        $this->em = $em;
        $this->sousTypeDocumentRepository = $sousTypeDocumentRepository;
    }

    /**
     * Récupère le sous-type de document MISSION avec cache
     */
    public function getMissionSousTypeDocument(): ?DomSousTypeDocument
    {
        $result = $this->cache->get('dom_mission_sous_type', function (ItemInterface $item) {
            $item->expiresAfter(3600); // Cache de 1 heure

            return $this->sousTypeDocumentRepository->createQueryBuilder('s')
                ->where('s.codeSousType = :code')
                ->setParameter('code', DomSousTypeDocument::CODE_SOUS_TYPE_MISSION)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        });

        // S'assurer que l'entité est gérée par l'EntityManager
        if ($result) {
            return $this->em->getReference(DomSousTypeDocument::class, $result->getId());
        }

        return $result;
    }

    /**
     * Récupère les catégories avec cache
     */
    public function getCategoriesByCriteria(int $sousTypeDocId, string $rmqDescription): array
    {
        $cacheKey = sprintf('dom_categories_%d_%s', $sousTypeDocId, md5($rmqDescription));

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($sousTypeDocId, $rmqDescription) {
            $item->expiresAfter(1800); // Cache de 30 minutes

            return $this->em->createQueryBuilder()
                ->select('DISTINCT c.id, c.description')
                ->from(DomCategorie::class, 'c')
                ->join('c.domIndemnites', 'i')
                ->join('i.domRmqId', 'r')
                ->join('i.domSousTypeDocumentId', 's')
                ->where('s.id = :sousTypeDoc')
                ->andWhere('r.description = :rmqDescription')
                ->setParameter('sousTypeDoc', $sousTypeDocId)
                ->setParameter('rmqDescription', $rmqDescription)
                ->getQuery()
                ->getArrayResult();
        });
    }

    /**
     * Récupère la RMQ avec cache
     */
    public function getRmqByDescription(string $description): ?DomRmq
    {
        $cacheKey = sprintf('dom_rmq_%s', md5($description));

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($description) {
            $item->expiresAfter(3600); // Cache de 1 heure

            return $this->em->getRepository(DomRmq::class)
                ->findOneBy(['description' => $description]);
        });
    }

    /**
     * Invalide le cache des catégories
     */
    public function invalidateCategoriesCache(): void
    {
        $this->cache->delete('dom_categories_*');
    }

    /**
     * Invalide tout le cache DOM
     */
    public function invalidateAllCache(): void
    {
        $this->cache->delete('dom_*');
    }
}
